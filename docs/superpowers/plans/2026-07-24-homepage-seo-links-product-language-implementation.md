# Homepage SEO Links and Product Language Cleanup Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Make all five new Lutsk landing pages directly reachable from the home page and footer, and remove confirmed Ukrainian-language errors from current and future product copy.

**Architecture:** A focused `ProductCopy` support class owns an explicit, reviewed replacement map. Product accessors and mutators apply it at application boundaries, while a data migration persists corrections to existing rows. The homepage and footer add server-rendered semantic navigation using existing design tokens and breakpoints.

**Tech Stack:** Laravel 7, PHP 7.2+, Eloquent, Blade, SCSS/Laravel Mix, Bootstrap 3 conventions, PHPUnit, Docker Compose, Node test runner.

## Global Constraints

- Normalize only reviewed, unambiguous language mistakes.
- Do not change HTML tags, brand names, model codes, measurements, prices, or technical values.
- Do not add hidden SEO copy, keyword lists, duplicate paragraphs, or invented claims.
- Keep exactly one H1 on the home page.
- Use crawlable server-rendered links and natural Ukrainian anchor text.
- Reuse Lato typography, the blue/yellow palette, visible focus states, and existing breakpoints.
- Add no frontend framework, runtime dependency, CMS field, or external service.
- Use test-driven development and commit only task-related files.

---

### Task 1: Create the reviewed product-copy normalizer

**Files:**
- Create: `app/Support/ProductCopy.php`
- Create: `tests/Unit/ProductCopyTest.php`

**Interfaces:**
- Consumes: `?string $value`
- Produces: `ProductCopy::normalize($value): ?string`

- [ ] **Step 1: Write the failing unit tests**

Create `tests/Unit/ProductCopyTest.php` with one data-driven correction test and
one preservation test:

```php
<?php

namespace Tests\Unit;

use App\Support\ProductCopy;
use Tests\TestCase;

class ProductCopyTest extends TestCase
{
    /**
     * @dataProvider incorrectCopyProvider
     */
    public function testItNormalizesReviewedUkrainianMistakes($dirty, $clean)
    {
        $this->assertSame($clean, ProductCopy::normalize($dirty));
    }

    public function incorrectCopyProvider()
    {
        return [
            ['Двері вхідін квартирні', 'Двері вхідні квартирні'],
            ['Двері вхідіні Імідж', 'Двері вхідні Імідж'],
            ['вуличні (зовнішіні)', 'вуличні (зовнішні)'],
            ['Накладки МДФ с двух сторін 16мм', 'Накладки МДФ з двох сторін 16мм'],
            ['В комплект входить поріг', 'До комплекту входить поріг'],
            ['Двері Підїздні', 'Двері Під’їзні'],
            ['2 контура на створці', '2 контури на створці'],
            ['Патина на 1 сторону', 'Патина з одного боку'],
            ['додатковий завіс', 'додаткова завіса'],
        ];
    }

    public function testItPreservesHtmlBrandsCodesMeasurementsAndCorrectCopy()
    {
        $copy = '<p>Двері вхідні S.A.P., МД 068, 86*203, лист 1.5 мм, МДФ з двох сторін.</p>';

        $this->assertSame($copy, ProductCopy::normalize($copy));
        $this->assertNull(ProductCopy::normalize(null));
    }
}
```

- [ ] **Step 2: Run the tests and verify RED**

Run:

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Unit/ProductCopyTest.php
```

Expected: FAIL because `App\Support\ProductCopy` does not exist.

- [ ] **Step 3: Implement the minimal normalizer**

Create `app/Support/ProductCopy.php`:

```php
<?php

namespace App\Support;

class ProductCopy
{
    private const REPLACEMENTS = [
        'вхідіні' => 'вхідні',
        'Вхідіні' => 'Вхідні',
        'вхідін' => 'вхідні',
        'Вхідін' => 'Вхідні',
        'зовнішіні' => 'зовнішні',
        'Зовнішіні' => 'Зовнішні',
        'зовнішін' => 'зовнішні',
        'Зовнішін' => 'Зовнішні',
        'с двух сторін' => 'з двох сторін',
        'С двух сторін' => 'З двох сторін',
        'В комплект входить' => 'До комплекту входить',
        'в комплект входить' => 'до комплекту входить',
        'Підїздні' => 'Під’їзні',
        'підїздні' => 'під’їзні',
        '2 контура' => '2 контури',
        'на 1 сторону' => 'з одного боку',
        'додатковий завіс' => 'додаткова завіса',
    ];

    public static function normalize($value)
    {
        if ($value === null || $value === '') {
            return $value;
        }

        return strtr($value, self::REPLACEMENTS);
    }
}
```

Keep longer variants before shorter variants so replacements cannot create
partial malformed words.

- [ ] **Step 4: Run the unit tests and verify GREEN**

Run the command from Step 2.

Expected: PASS.

- [ ] **Step 5: Commit**

```powershell
git add app/Support/ProductCopy.php tests/Unit/ProductCopyTest.php
git commit -m "feat: normalize reviewed Ukrainian product copy"
```

---

### Task 2: Normalize product fields when reading and saving

**Files:**
- Modify: `app/Models/Product.php`
- Create: `tests/Unit/ProductCopyModelTest.php`

**Interfaces:**
- Consumes: `ProductCopy::normalize(?string): ?string`
- Produces: normalized `title`, `text`, `description`, `seo_title`, and `seo_description` model attributes

- [ ] **Step 1: Write failing model-boundary tests**

Create `tests/Unit/ProductCopyModelTest.php`:

```php
<?php

namespace Tests\Unit;

use App\Models\Product;
use Tests\TestCase;

class ProductCopyModelTest extends TestCase
{
    public function testDirtyValuesAreNormalizedBeforeSaving()
    {
        $product = new Product();
        $product->title = 'Двері вхідіні Престиж';
        $product->text = '<p>Накладки МДФ с двух сторін</p>';
        $product->seo_description = 'В комплект входить поріг';

        $this->assertSame('Двері вхідні Престиж', $product->getAttributes()['title']);
        $this->assertSame('<p>Накладки МДФ з двох сторін</p>', $product->getAttributes()['text']);
        $this->assertSame('До комплекту входить поріг', $product->getAttributes()['seo_description']);
    }

    public function testLegacyRawValuesAreNormalizedWhenRead()
    {
        $product = new Product();
        $product->setRawAttributes([
            'title' => 'Двері вхідін Оберіг',
            'description' => 'Двері Підїздні',
        ], true);

        $this->assertSame('Двері вхідні Оберіг', $product->title);
        $this->assertSame('Двері Під’їзні', $product->description);
    }
}
```

- [ ] **Step 2: Run the tests and verify RED**

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Unit/ProductCopyModelTest.php
```

Expected: FAIL because `Product` does not normalize these attributes.

- [ ] **Step 3: Add focused accessors and mutators**

Import `App\Support\ProductCopy` in `app/Models/Product.php`. Add a private
setter helper and explicit Laravel accessor/mutator pairs:

```php
private function setNormalizedAttribute($key, $value)
{
    $this->attributes[$key] = ProductCopy::normalize($value);
}

public function getTitleAttribute($value)
{
    return ProductCopy::normalize($value);
}

public function setTitleAttribute($value)
{
    $this->setNormalizedAttribute('title', $value);
}
```

Repeat the pair for `text`, `description`, `seo_title`, and
`seo_description`, using their exact attribute keys. Do not normalize
`alias`, `keywords`, `extra_fields`, or price fields.

- [ ] **Step 4: Run focused and catalog selector tests**

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Unit/ProductCopyModelTest.php tests/Unit/CommercialLandingTest.php
```

Expected: PASS.

- [ ] **Step 5: Commit**

```powershell
git add app/Models/Product.php tests/Unit/ProductCopyModelTest.php
git commit -m "feat: enforce clean product copy at model boundaries"
```

---

### Task 3: Persist corrections to existing product rows

**Files:**
- Create: `database/migrations/2026_07_24_090000_normalize_ukrainian_product_copy.php`
- Create: `tests/Feature/ProductCopyMigrationTest.php`

**Interfaces:**
- Consumes: `ProductCopy::normalize(?string): ?string`
- Produces: corrected stored values for `products.title`, `text`, `description`, `seo_title`, and `seo_description`

- [ ] **Step 1: Write the failing migration test**

Create a database-transaction feature test that writes raw dirty values, runs
the migration class, and reads raw values back:

```php
<?php

namespace Tests\Feature;

use App\Models\Product;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProductCopyMigrationTest extends TestCase
{
    use DatabaseTransactions;

    public function testMigrationPersistsReviewedCorrectionsOnly()
    {
        $product = Product::published()->firstOrFail();

        DB::table('products')->where('id', $product->id)->update([
            'title' => 'Двері вхідіні Тест',
            'text' => '<p>Накладки МДФ с двух сторін 16мм</p>',
            'description' => 'S.A.P., МД 068, 86*203',
        ]);

        require_once database_path('migrations/2026_07_24_090000_normalize_ukrainian_product_copy.php');
        (new \NormalizeUkrainianProductCopy())->up();

        $stored = DB::table('products')->where('id', $product->id)->first();
        $this->assertSame('Двері вхідні Тест', $stored->title);
        $this->assertSame('<p>Накладки МДФ з двох сторін 16мм</p>', $stored->text);
        $this->assertSame('S.A.P., МД 068, 86*203', $stored->description);
    }
}
```

- [ ] **Step 2: Run the test and verify RED**

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Feature/ProductCopyMigrationTest.php
```

Expected: FAIL because the migration file/class does not exist.

- [ ] **Step 3: Implement the data migration**

Create the named migration class `NormalizeUkrainianProductCopy`. In `up()`:

```php
$fields = ['title', 'text', 'description', 'seo_title', 'seo_description'];

DB::table('products')
    ->select(array_merge(['id'], $fields))
    ->orderBy('id')
    ->chunkById(100, function ($products) use ($fields) {
        foreach ($products as $product) {
            $updates = [];

            foreach ($fields as $field) {
                $clean = ProductCopy::normalize($product->{$field});
                if ($clean !== $product->{$field}) {
                    $updates[$field] = $clean;
                }
            }

            if ($updates) {
                DB::table('products')->where('id', $product->id)->update($updates);
            }
        }
    });
```

Import `App\Support\ProductCopy`, `Illuminate\Database\Migrations\Migration`,
and `Illuminate\Support\Facades\DB`. Leave `down()` empty with a comment that
known language errors must not be restored.

- [ ] **Step 4: Run migration and normalizer tests**

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Feature/ProductCopyMigrationTest.php tests/Unit/ProductCopyTest.php tests/Unit/ProductCopyModelTest.php
```

Expected: PASS.

- [ ] **Step 5: Audit the live local database without modifying it manually**

Run the migration through Laravel:

```powershell
docker compose exec -T app php artisan migrate --force
```

Then query published products for the reviewed core variants and verify every
count is zero:

```powershell
docker compose exec -T app php artisan tinker --execute="echo App\Models\Product::published()->where(function (`$q) { `$q->where('title','like','%вхідін%')->orWhere('text','like','%вхідін%')->orWhere('text','like','%двух%')->orWhere('text','like','%зовнішін%')->orWhere('text','like','%комплект входить%')->orWhere('title','like','%Підїзд%'); })->count();"
```

Expected: `0`.

- [ ] **Step 6: Commit**

```powershell
git add database/migrations/2026_07_24_090000_normalize_ukrainian_product_copy.php tests/Feature/ProductCopyMigrationTest.php
git commit -m "fix: clean existing Ukrainian product copy"
```

---

### Task 4: Add direct SEO navigation on the homepage and footer

**Files:**
- Modify: `tests/Feature/SeoKnowledgeTest.php`
- Modify: `resources/views/client/main/index.blade.php`
- Modify: `resources/views/client/shared/footer.blade.php`
- Modify: `resources/client/scss/_common.scss`
- Modify: `public/mix-manifest.json` only when the tracked hash changes

**Interfaces:**
- Consumes: five existing landing routes
- Produces: `.home-commercial-links` semantic navigation and direct footer links

- [ ] **Step 1: Write the failing homepage-link test**

Add a data-driven test to `tests/Feature/SeoKnowledgeTest.php`:

```php
public function testHomepageAndFooterLinkDirectlyToRequestedCommercialPages()
{
    $paths = [
        '/metalovi-dveri-lutsk',
        '/bronovani-dveri-lutsk',
        '/vkhidni-dveri-v-budynok-lutsk',
        '/vkhidni-dveri-v-kvartyru-lutsk',
        '/mizhkimnatni-dveri-z-montazhem-lutsk',
    ];

    $response = $this->get('/')->assertStatus(200)
        ->assertSee('<nav class="home-commercial-links"', false)
        ->assertSee('aria-labelledby="home-commercial-links-title"', false)
        ->assertSee('Популярні рішення для дверей у Луцьку');

    foreach ($paths as $path) {
        $response->assertSee('href="' . $path . '"', false);
    }

    $this->assertSame(1, preg_match_all('/<h1\b/i', $response->getContent()));
}
```

- [ ] **Step 2: Run the test and verify RED**

```powershell
docker compose exec -T app vendor/bin/phpunit --filter testHomepageAndFooterLinkDirectlyToRequestedCommercialPages
```

Expected: FAIL because `.home-commercial-links` and the five direct links are
not all present.

- [ ] **Step 3: Add the homepage navigation data and markup**

Define `$commercialLinks` beside `$homeCategories`, with five arrays containing
`title`, `text`, and `url`. Use intent-specific Ukrainian titles and one-sentence
descriptions without prices, availability, or guarantees.

Render before the existing general category section:

```blade
<section class="home-commercial-links-section">
    <div class="container">
        <nav class="home-commercial-links" aria-labelledby="home-commercial-links-title">
            <h2 id="home-commercial-links-title">Популярні рішення для дверей у Луцьку</h2>
            <p>Оберіть тип дверей і перейдіть до моделей, особливостей комплектації, монтажу та відповідей на поширені запитання.</p>
            <div class="home-commercial-links__grid">
                @foreach($commercialLinks as $link)
                    <a href="{{ $link['url'] }}">
                        <strong>{{ $link['title'] }}</strong>
                        <span>{{ $link['text'] }}</span>
                        <span class="home-commercial-links__action">Переглянути</span>
                    </a>
                @endforeach
            </div>
        </nav>
    </div>
</section>
```

- [ ] **Step 4: Expand the footer list**

Add the same five destination URLs under `Популярні запити`, using compact
labels. Preserve the existing `/vkhidni-dveri-lutsk`,
`/mizhkimnatni-dveri-lutsk`, `/dveri-z-montazhem-lutsk`, and `/dveri-volyn`
links.

- [ ] **Step 5: Add responsive styles**

In `_common.scss`, style:

```scss
.home-commercial-links-section {
    padding: 64px 0;
    background: #f5f5f5;
}

.home-commercial-links__grid {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 20px;
}
```

Use existing `$blue`, `$yellow`, `$black`, card borders, and focus patterns.
Switch to two columns at `991px` and one at `640px`. Give links a visible
`:focus` outline and use `overflow-wrap: anywhere` for narrow screens.

- [ ] **Step 6: Build assets and verify GREEN**

```powershell
npm test
npm run production
docker compose exec -T app vendor/bin/phpunit --filter testHomepageAndFooterLinkDirectlyToRequestedCommercialPages
```

Expected: Node tests, production build, and focused PHPUnit test all pass.

- [ ] **Step 7: Commit**

```powershell
git add resources/views/client/main/index.blade.php resources/views/client/shared/footer.blade.php resources/client/scss/_common.scss tests/Feature/SeoKnowledgeTest.php public/mix-manifest.json
git commit -m "feat: link homepage to commercial landing pages"
```

Only stage `public/mix-manifest.json` when it changed; compiled
`public/assets` files remain ignored by the project.

---

### Task 5: Full language, SEO, and Git verification

**Files:**
- Verify all files changed in Tasks 1–4

**Interfaces:**
- Consumes: complete implementation
- Produces: verified and pushed task branch

- [ ] **Step 1: Run the complete automated suite**

```powershell
docker compose exec -T app vendor/bin/phpunit
npm test
npm run production
```

Expected: all commands exit zero.

- [ ] **Step 2: Run HTTP smoke checks**

Request the home page and representative corrected product pages through
`http://localhost:8081`. Verify HTTP 200, one H1 on the home page, all five
direct links, corrected product title/body copy, and no reviewed bad variants
in the rendered HTML.

- [ ] **Step 3: Review repository state**

```powershell
git status --short
git diff --check
git diff
git log --oneline --decorate -10
```

Confirm no `.env`, database dump, generated ignored assets, credentials, or
unrelated files are staged.

- [ ] **Step 4: Commit any final verified correction**

If and only if verification required a correction:

```powershell
git add -- <exact-related-files>
git commit -m "fix: finalize product copy and homepage SEO links"
```

- [ ] **Step 5: Push**

```powershell
git push -u origin HEAD
```

Expected: `codex/docker-port-8081` is up to date on `origin`.
