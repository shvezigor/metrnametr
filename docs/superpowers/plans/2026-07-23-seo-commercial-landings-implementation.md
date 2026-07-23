# SEO Commercial Landing Pages Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add truthful, catalog-backed commercial blocks and five missing local landing URLs while strengthening sitemap, structured data, internal links, real-work proof, and relevant knowledge articles.

**Architecture:** Extend the current configuration-driven `SeoContent` landing system. A focused `CommercialLanding` support class selects and presents confirmed `Product` data, while reusable Blade partials render shared conversion sections and page configuration supplies page-specific copy and links.

**Tech Stack:** Laravel 7, PHP 7.2+, Eloquent, Blade, Bootstrap 3 conventions, SCSS/Laravel Mix, PHPUnit, Node test runner, Docker Compose.

## Global Constraints

- Use only published `Product` records for model data and only `RealWorks` cases for installation proof.
- Render a numeric price only when the stored product price is greater than zero.
- Render `Ціну уточнюйте після підбору комплектації` when no confirmed price exists.
- Omit missing optional specifications, availability, and unrelated real-work previews.
- Do not invent prices, status, specifications, guarantees, locations, or service promises.
- Preserve the existing Lato typography, colors, Bootstrap breakpoints, shared components, and CTA workflow.
- Do not add a frontend framework, runtime dependency, CMS table, admin form, or upload backend.
- Every page has one H1, an absolute canonical, crawlable route, sitemap entry, Open Graph image, breadcrumbs, and valid applicable schema.
- Every image has meaningful Ukrainian alt text and fixed dimensions where the source provides them.
- Mobile pages must not introduce horizontal scrolling.

---

### Task 1: Lock the new route and metadata contract

**Files:**
- Modify: `tests/Feature/SeoKnowledgeTest.php`
- Modify: `routes/web.php`
- Modify: `config/seo_landings.php`

**Interfaces:**
- Consumes: `SeoController@landing`, `SeoContent::landingPage(string $slug)`
- Produces: five new named `seo.*` GET routes and five complete landing configuration entries

- [ ] **Step 1: Write a failing feature test for the five missing pages**

Add a data-driven test that requests:

```php
[
    '/metalovi-dveri-lutsk' => [
        'title' => 'Металеві двері Луцьк — вхідні двері від виробника | Метр на Метр',
        'h1' => 'Металеві двері у Луцьку',
    ],
    '/bronovani-dveri-lutsk' => [
        'title' => 'Броньовані двері Луцьк — підбір, доставка, монтаж | Метр на Метр',
        'h1' => 'Броньовані двері у Луцьку',
    ],
    '/vkhidni-dveri-v-budynok-lutsk' => [
        'title' => 'Вхідні двері в будинок Луцьк — утеплення, терморозрив, монтаж | Метр на Метр',
        'h1' => 'Вхідні двері в будинок у Луцьку',
    ],
    '/vkhidni-dveri-v-kvartyru-lutsk' => [
        'title' => 'Вхідні двері в квартиру Луцьк — металеві двері з монтажем | Метр на Метр',
        'h1' => 'Вхідні двері в квартиру у Луцьку',
    ],
    '/mizhkimnatni-dveri-z-montazhem-lutsk' => [
        'title' => 'Міжкімнатні двері з монтажем у Луцьку | Метр на Метр',
        'h1' => 'Міжкімнатні двері з монтажем у Луцьку',
    ],
]
```

For every response assert HTTP 200, the exact `<title>`, exact H1, canonical,
Open Graph image, `BreadcrumbList`, `Service`, and no second H1.

- [ ] **Step 2: Run the test and verify RED**

Run:

```powershell
docker compose exec -T app vendor/bin/phpunit --filter testRequestedCommercialLandingRoutesExposeExactMetadata
```

Expected: FAIL because the five routes return 404.

- [ ] **Step 3: Add the routes and minimum complete configuration**

Register the five explicit routes before the knowledge and product routes. Add
configuration entries with `path`, `title`, `description`, `h1`, two or three
intro paragraphs, intent-specific sections, five to seven FAQ entries,
`og_image`, `catalog_match`, `work_contexts`, and `internal_links`.

Use these match intents:

```php
'metalovi-dveri-lutsk' => ['type_terms' => ['Вхідні'], 'category_terms' => []],
'bronovani-dveri-lutsk' => ['type_terms' => ['Вхідні'], 'category_terms' => ['броньован']],
'vkhidni-dveri-v-budynok-lutsk' => ['type_terms' => ['Вхідні'], 'category_terms' => ['термо', 'будин']],
'vkhidni-dveri-v-kvartyru-lutsk' => ['type_terms' => ['Вхідні'], 'category_terms' => ['квартир']],
'mizhkimnatni-dveri-z-montazhem-lutsk' => ['type_terms' => ['Міжкімнатні'], 'category_terms' => []],
```

Configuration matching is an allow-list hint; it must never manufacture a
category or product attribute.

- [ ] **Step 4: Run the focused test and verify GREEN**

Run the command from Step 2.

Expected: PASS.

- [ ] **Step 5: Commit**

```powershell
git add routes/web.php config/seo_landings.php tests/Feature/SeoKnowledgeTest.php
git commit -m "feat: add requested Lutsk door landing routes"
```

### Task 2: Select and present truthful catalog products

**Files:**
- Create: `app/Support/CommercialLanding.php`
- Create: `tests/Unit/CommercialLandingTest.php`
- Modify: `app/Http/Controllers/SeoController.php`

**Interfaces:**
- Consumes: `array $landing`, `Product`, `SeoContent::productSpecifications()`
- Produces:
  - `CommercialLanding::products(array $landing, int $limit = 12): Collection`
  - `CommercialLanding::card(Product $product): array`
  - controller variable `$commercialProducts`

- [ ] **Step 1: Write failing unit tests for product truthfulness**

Cover these behaviors with real in-memory model objects and database-backed
relationship tests:

```php
$card = CommercialLanding::card($product);

$this->assertSame('Ціну уточнюйте після підбору комплектації', $card['price']);
$this->assertArrayNotHasKey('leaf_thickness', $card['specifications']);
$this->assertSame('/product/test-door', parse_url($card['url'], PHP_URL_PATH));
```

Also assert that selection:

- includes published products only;
- uses configured type/category text terms case-insensitively;
- returns unique IDs;
- is deterministic by label, update date, then ID;
- returns at most twelve products;
- returns an empty collection rather than unrelated fallback products.

- [ ] **Step 2: Run the unit test and verify RED**

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Unit/CommercialLandingTest.php
```

Expected: FAIL because `CommercialLanding` does not exist.

- [ ] **Step 3: Implement the selector and card presenter**

Use `Product::published()->with(['categories.type', 'images'])`. Apply a
`whereHas('categories', ...)` condition for each configured allow-list group.
Escape SQL wildcard characters before using `%term%` matching. Do not use a
broad published-product fallback when no term matches.

The card shape is:

```php
[
    'id' => (int) $product->id,
    'title' => $product->title,
    'image' => $product->cover,
    'image_alt' => $product->title . ' — двері з каталогу Метр на Метр',
    'type' => $confirmedTypeOrCategory,
    'specifications' => array_filter([
        'leaf_thickness' => $specs['leaf_thickness'],
        'insulation' => $specs['insulation'],
        'locks' => $specs['locks'],
        'finish' => $specs['finish'],
        'thermal_break' => $specs['thermal_break'],
    ]),
    'price' => $product->price > 0
        ? number_format($product->price, 0, ',', ' ') . ' грн'
        : 'Ціну уточнюйте після підбору комплектації',
    'availability' => $specs['availability'],
    'url' => $product->location,
]
```

Do not infer a status from the product label or publication flag.

- [ ] **Step 4: Pass products from the controller**

Resolve the products once per landing and expose them to the view. Keep the
existing title, description, canonical, FAQ, RealWorks preview, and schema
variables intact.

- [ ] **Step 5: Run focused and existing SEO tests**

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Unit/CommercialLandingTest.php
docker compose exec -T app vendor/bin/phpunit tests/Feature/SeoKnowledgeTest.php
```

Expected: PASS.

- [ ] **Step 6: Commit**

```powershell
git add app/Support/CommercialLanding.php app/Http/Controllers/SeoController.php tests/Unit/CommercialLandingTest.php
git commit -m "feat: select truthful products for SEO landings"
```

### Task 3: Build reusable commercial Blade sections

**Files:**
- Create: `resources/views/client/seo/partials/products.blade.php`
- Create: `resources/views/client/seo/partials/installation.blade.php`
- Create: `resources/views/client/seo/partials/order-process.blade.php`
- Create: `resources/views/client/seo/partials/price-factors.blade.php`
- Create: `resources/views/client/seo/partials/internal-links.blade.php`
- Create: `resources/views/client/seo/partials/enquiry-cta.blade.php`
- Modify: `resources/views/client/seo/landing.blade.php`
- Modify: `tests/Feature/SeoKnowledgeTest.php`

**Interfaces:**
- Consumes: `$landing`, `$commercialProducts`, `$realWorksPreview`
- Produces: semantic commercial sections with stable `commercial-*` class names

- [ ] **Step 1: Write a failing rendering test**

For `/vkhidni-dveri-lutsk` and each requested page, assert:

- `Популярні моделі` or the configured popular-model heading;
- a product link and truthful price fallback when a matching fixture exists;
- `Що входить у підбір і монтаж`;
- the six numbered order steps in order;
- `Що впливає на ціну`;
- a mid-page and final CTA;
- catalog and adjacent-page links;
- one H1;
- no empty `<ul>` or empty product grid.

- [ ] **Step 2: Run the test and verify RED**

```powershell
docker compose exec -T app vendor/bin/phpunit --filter testCommercialLandingsRenderReusableConversionSections
```

Expected: FAIL because the shared sections do not exist.

- [ ] **Step 3: Implement the product grid partial**

Render an `<article>` per card. Use a `<picture>` only when a real WebP variant
is available; otherwise use the confirmed image URL with `loading="lazy"` and
the card alt. Render only non-empty specification rows and availability. Show
the price fallback exactly. Use existing contact and product routes for:

- `Запитати ціну`;
- `Замовити замір`;
- `Детальніше`.

When the collection is empty, render a short explanation plus catalog and
consultation links, not an empty grid.

- [ ] **Step 4: Implement the service, process, pricing, linking, and CTA partials**

The installation partial renders the approved five service items and
context-specific explanation. The order partial renders an ordered list with
six steps. The price partial renders the nine approved price factors. Internal
links come only from the landing configuration. The CTA partial uses existing
GA event names and never renders a file input.

- [ ] **Step 5: Refactor the main landing template**

Remove hard-coded sections that duplicate the new partials, especially current
entrance-only package/process/install blocks. Preserve the current entrance
commercial hero and existing long-form page copy. Place products after the
intro, real-work preview before FAQ, and CTA blocks in the middle and at the
bottom.

- [ ] **Step 6: Run the focused and full feature test files**

```powershell
docker compose exec -T app vendor/bin/phpunit --filter testCommercialLandingsRenderReusableConversionSections
docker compose exec -T app vendor/bin/phpunit tests/Feature/SeoKnowledgeTest.php tests/Feature/RealWorksTest.php
```

Expected: PASS.

- [ ] **Step 7: Commit**

```powershell
git add resources/views/client/seo tests/Feature/SeoKnowledgeTest.php
git commit -m "feat: render reusable landing conversion sections"
```

### Task 4: Complete page copy, internal links, and real-work contexts

**Files:**
- Modify: `config/seo_landings.php`
- Modify: `config/seo_landings_overrides.php`
- Modify: `config/real_works.php`
- Modify: `resources/views/client/main/index.blade.php`
- Modify: `resources/views/client/shared/footer.blade.php`
- Modify: `tests/Feature/SeoKnowledgeTest.php`
- Modify: `tests/Feature/RealWorksTest.php`

**Interfaces:**
- Consumes: landing `internal_links`, RealWorks preview context keys
- Produces: complete Ukrainian landing copy and a connected commercial link graph

- [ ] **Step 1: Write failing link and content tests**

Assert every requested landing has two or three intro paragraphs, five to seven
FAQ entries, contextual installation copy, at least three relevant internal
links, and relevant real-work cases only. Assert the home page and footer expose
`/nashi-roboty` and useful local pages.

- [ ] **Step 2: Run the tests and verify RED**

```powershell
docker compose exec -T app vendor/bin/phpunit --filter "testRequestedLandingContentIsComplete|testCommercialInternalLinkGraphIsConnected"
```

Expected: FAIL on missing copy, links, or preview contexts.

- [ ] **Step 3: Complete the page-specific Ukrainian content**

Write natural copy around the supplied focus for metal, armored, house,
apartment, installation, and interior-installation pages. Avoid repeated exact
keyphrases within adjacent paragraphs. Do not add price ranges, stock claims,
installation duration, or warranty promises.

- [ ] **Step 4: Configure relevant real-work previews**

Map entrance/house/apartment pages only to confirmed entrance cases and the
interior-installation page only to confirmed interior cases. Add no new case
content.

- [ ] **Step 5: Add navigation links**

Reuse existing useful-links/footer patterns. Keep the list compact and
contextual; do not place all URLs in every section.

- [ ] **Step 6: Run SEO and RealWorks tests**

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Feature/SeoKnowledgeTest.php tests/Feature/RealWorksTest.php
```

Expected: PASS.

- [ ] **Step 7: Commit**

```powershell
git add config/seo_landings.php config/seo_landings_overrides.php config/real_works.php resources/views/client/main/index.blade.php resources/views/client/shared/footer.blade.php tests/Feature
git commit -m "feat: complete commercial landing content and links"
```

### Task 5: Finish schema, sitemap, Open Graph, and AI discovery

**Files:**
- Modify: `app/Support/SeoContent.php`
- Modify: `app/Http/Controllers/SeoController.php`
- Modify: `tests/Feature/SeoKnowledgeTest.php`

**Interfaces:**
- Consumes: landing `og_image`, product card images, configured landing paths
- Produces: `ImageObject` schema where confirmed images exist and complete discovery outputs

- [ ] **Step 1: Write failing technical SEO tests**

Assert all requested URLs:

- appear once in `SeoContent::sitemapUrls()` and rendered XML;
- appear in `llms-full.txt`;
- use an absolute canonical and Open Graph image;
- emit `BreadcrumbList`, `Service`, and non-empty `FAQPage`;
- emit `ImageObject` only with absolute confirmed image URLs;
- are not blocked by `robots.txt`.

Also assert the sitemap excludes localhost, `%20`, query strings, and duplicate
locations.

- [ ] **Step 2: Run the test and verify RED**

```powershell
docker compose exec -T app vendor/bin/phpunit --filter testRequestedCommercialLandingsAreTechnicallyDiscoverable
```

Expected: FAIL on missing image schema or discovery entries.

- [ ] **Step 3: Add confirmed image schema**

Add a focused helper that returns `null` for an empty/unconfirmed image and an
`ImageObject` array for a confirmed URL. Filter null schema entries before the
layout serializes them.

- [ ] **Step 4: Complete sitemap and discovery enumeration**

Rely on `SeoContent::landingPages()` as the single enumeration source so new
configured pages automatically appear once. Preserve historical last-modified
logic and canonical filtering.

- [ ] **Step 5: Run technical SEO tests**

```powershell
docker compose exec -T app vendor/bin/phpunit --filter "testRequestedCommercialLandingsAreTechnicallyDiscoverable|testSitemap"
```

Expected: PASS.

- [ ] **Step 6: Commit**

```powershell
git add app/Support/SeoContent.php app/Http/Controllers/SeoController.php tests/Feature/SeoKnowledgeTest.php
git commit -m "feat: expose commercial landings to search and AI"
```

### Task 6: Add responsive, accessible commercial styling

**Files:**
- Create: `resources/client/scss/_commercial-landings.scss`
- Modify: `resources/client/scss/app.scss`
- Modify: `tests/Feature/SeoKnowledgeTest.php`

**Interfaces:**
- Consumes: `commercial-*` class names from Task 3
- Produces: responsive product grid, process rail, pricing list, and CTA layout

- [ ] **Step 1: Write a failing structural accessibility test**

Assert cards use articles, process steps use `<ol>`, every product image has
non-empty alt, CTAs have descriptive text, and no image lacks width/height when
dimensions are known.

- [ ] **Step 2: Run the test and verify RED**

```powershell
docker compose exec -T app vendor/bin/phpunit --filter testCommercialLandingMarkupIsAccessible
```

Expected: FAIL on missing semantic or image attributes.

- [ ] **Step 3: Implement SCSS with current design tokens**

Use existing variables and mixins. Desktop uses a three-column product grid,
tablet two columns, and mobile one column. CTA groups wrap, long prices break
safely, product images use `object-fit: contain`, and focus states remain
visible. Do not change global typography or button styles.

- [ ] **Step 4: Build production assets**

```powershell
npm test
npm run production
```

Expected: Node tests pass and Laravel Mix exits zero.

- [ ] **Step 5: Run the accessibility test**

Run the command from Step 2.

Expected: PASS.

- [ ] **Step 6: Commit**

```powershell
git add resources/client/scss resources/views/client/seo tests/Feature/SeoKnowledgeTest.php public/css public/mix-manifest.json
git commit -m "feat: style responsive commercial landing blocks"
```

Only stage compiled assets that are already tracked by the project.

### Task 7: Audit and strengthen published knowledge articles

**Files:**
- Modify: `config/knowledge_articles.php`
- Modify: `app/Support/KnowledgeArticleFactory.php`
- Modify: `resources/views/client/knowledge/show.blade.php`
- Modify: `tests/Feature/SeoKnowledgeTest.php`
- Modify: `tests/Feature/KnowledgePlanTest.php`

**Interfaces:**
- Consumes: published knowledge article arrays and landing route map
- Produces: natural article sections, relevant commercial links, and auditable heading/FAQ rules

- [ ] **Step 1: Write failing audit tests**

For every published article assert:

- non-empty title and description;
- one rendered H1;
- logical H2/H3 markup;
- no FAQ question duplicates a section heading exactly;
- catalog link and at least one relevant local commercial link;
- image and alt metadata;
- no repeated focus phrase in consecutive section headings.

- [ ] **Step 2: Run audit tests and capture exact failures**

```powershell
docker compose exec -T app vendor/bin/phpunit --filter "testPublishedKnowledgeArticlesPassCommercialAudit|testKnowledge"
```

Expected: FAIL only for concrete articles/rules that need correction.

- [ ] **Step 3: Fix only demonstrated article issues**

For each failure, edit the configured article or factory output to remove
mechanical repetition, add a relevant comparison/buyer-mistake/pre-order
section, or add a contextual catalog/local-page link. Do not bulk-rewrite
articles that already pass.

- [ ] **Step 4: Re-run knowledge tests**

```powershell
docker compose exec -T app vendor/bin/phpunit tests/Feature/KnowledgePlanTest.php tests/Feature/SeoKnowledgeTest.php
```

Expected: PASS.

- [ ] **Step 5: Commit**

```powershell
git add config/knowledge_articles.php app/Support/KnowledgeArticleFactory.php resources/views/client/knowledge/show.blade.php tests/Feature
git commit -m "feat: strengthen knowledge article conversion paths"
```

Stage only files that actually changed after the audit.

### Task 8: Full verification and Git finalization

**Files:**
- Verify all files changed in Tasks 1–7

**Interfaces:**
- Consumes: complete implementation
- Produces: verified branch ready for review

- [ ] **Step 1: Run the full automated suite**

```powershell
docker compose exec -T app vendor/bin/phpunit
npm test
npm run production
```

Expected: all commands exit zero.

- [ ] **Step 2: Verify HTTP and structured output**

Request every new and modified landing through `http://localhost:8081`. Confirm
HTTP 200, canonical, title, one H1, schema JSON, real images, working product
links, working contact CTAs, and sitemap presence.

- [ ] **Step 3: Verify rendered desktop and mobile UI**

Use the browser at a desktop viewport and approximately 390 px mobile width.
Inspect the first viewport, product grid, installation/process blocks, real-work
preview, FAQ, final CTA, keyboard focus, image loading, and horizontal overflow.

- [ ] **Step 4: Review the final diff**

```powershell
git status --short
git diff --check
git diff --stat
git diff
```

Confirm no `.env`, database dump, cache, credentials, unrelated generated
files, or pre-existing work is staged.

- [ ] **Step 5: Commit any verified final corrections**

```powershell
git add -u -- app config public resources routes tests
git commit -m "fix: finalize SEO commercial landing verification"
```

Skip this commit when no correction is needed.

- [ ] **Step 6: Push**

```powershell
git push -u origin HEAD
```

Expected: the current task branch is updated on `origin`.
