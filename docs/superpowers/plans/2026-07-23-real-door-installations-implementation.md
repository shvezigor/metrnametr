# Real Door Installations Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Build `/nashi-roboty` as a fast, accessible, SEO-ready collection of verified door installations and add compact real-work proof blocks to the homepage and three local landing pages.

**Architecture:** `config/real_works.php` is the content source of truth and `App\Support\RealWorks` exposes stable page, case, video, and context-preview APIs. A dedicated controller renders one Blade page; reusable picture and preview partials serve the page and existing landings. A small CommonJS module handles filters, lightbox behavior, and click-to-load video without adding dependencies.

**Tech Stack:** Laravel 7, PHP 7.2+, Blade, Laravel Mix 5, SCSS, vanilla JavaScript/CommonJS, PHPUnit 8, Node built-in test runner, Pillow/OpenCV for one-time media preparation, Docker Compose.

## Global Constraints

- Use only photos where the door is visibly installed; exclude showroom, production, advertising collage, screenshot, duplicate, and unrelated media.
- Do not invent exact addresses, towns, product specifications, ratings, or client details.
- Allowed location wording is limited to `Луцьк`, `Волинська область`, `для квартири`, and `для приватного будинку`.
- Preserve the existing header and current `resources/client/images/logo-2.png`; do not create a replacement logo.
- Use `/nashi-roboty` as the only canonical works URL.
- Keep the editorial open-layout direction: large real photos, no nested cards, restrained CTA treatment, and no advertising-heavy hero.
- Add no UI framework or runtime dependency.
- Use WebP with JPEG fallback, explicit dimensions, and lazy loading below the first viewport.
- Do not fetch MP4 before user activation.
- `video (9).mp4` is excluded because manual frame review shows unrelated police/social-media footage.
- `IMG_3157.MP4` is the only relevant supplied video; it is H.264, 1072×1904, approximately 14.94 seconds and 11.83 MB.
- Because no FFmpeg encoder is installed, keep the relevant H.264 file unchanged behind click-to-load behavior; document the deferred loading as the performance safeguard.
- Real supplied media replaces generated visual assets; do not use Image Gen for installation evidence.

---

## File Structure

### New files

- `config/real_works.php` — page metadata, five verified case records, photo metadata, video metadata, filters, and preview ordering.
- `app/Support/RealWorks.php` — stable accessors and context filtering around the config.
- `app/Http/Controllers/RealWorksController.php` — builds the page view model.
- `resources/views/client/works/index.blade.php` — complete works page.
- `resources/views/client/works/partials/picture.blade.php` — `<picture>` output with WebP, JPEG, dimensions, alt, and loading controls.
- `resources/views/client/works/partials/case.blade.php` — one editorial case section.
- `resources/views/client/works/partials/video.blade.php` — poster-first video control without initial `src`.
- `resources/views/client/shared/real-works-preview.blade.php` — compact reusable proof block.
- `resources/client/scss/_real-works.scss` — page, preview, lightbox, responsive, focus, and reduced-motion styles.
- `resources/client/js/real-works.js` — filters, lightbox, focus management, and click-to-load video.
- `tests/Feature/RealWorksTest.php` — content, assets, page, metadata, schema, preview, sitemap, and AI regression coverage.
- `tests/js/real-works.test.js` — pure filter and lightbox-index behavior.
- `resources/client/images/real-works/*` — optimized approved photos, posters, OG image, and deferred video.

### Modified files

- `routes/web.php` — named `/nashi-roboty` route.
- `app/Support/SeoContent.php` — `ImageGallery` schema and sitemap entry.
- `app/Http/Controllers/MainController.php` — homepage preview data.
- `app/Http/Controllers/SeoController.php` — landing preview data and AI-text references.
- `resources/views/client/main/index.blade.php` — homepage preview placement.
- `resources/views/client/seo/landing.blade.php` — preview placement for three approved landing paths.
- `resources/views/client/knowledge/for-ai-agents.blade.php` — works-page citation guidance.
- `resources/client/scss/app.scss` — import real-work styles.
- `resources/client/js/app.js` — require real-work behavior.
- `package.json` — deterministic `npm test` command using Node’s built-in runner.
- `tests/Feature/SeoKnowledgeTest.php` — keep broad sitemap/AI regression assertions aligned with the new URL.

---

### Task 1: Define the verified real-work content contract

**Files:**

- Create: `config/real_works.php`
- Create: `app/Support/RealWorks.php`
- Create: `tests/Feature/RealWorksTest.php`

**Interfaces:**

- Produces: `RealWorks::page(): array`
- Produces: `RealWorks::filters(): array`
- Produces: `RealWorks::cases(): array`
- Produces: `RealWorks::videos(): array`
- Produces: `RealWorks::featured(string $context): array`
- Produces: `RealWorks::allImages(): array`

- [ ] **Step 1: Write the failing content-contract tests**

Create `tests/Feature/RealWorksTest.php`:

```php
<?php

namespace Tests\Feature;

use App\Support\RealWorks;
use Tests\TestCase;

class RealWorksTest extends TestCase
{
    public function testRealWorkContentHasFiveVerifiedCasesAndSafeLocations()
    {
        $cases = RealWorks::cases();
        $allowedLocations = ['Луцьк', 'Волинська область', 'для квартири', 'для приватного будинку'];

        $this->assertCount(5, $cases);
        $this->assertSame($cases->count(), $cases->pluck('id')->unique()->count());

        $cases->each(function ($case) use ($allowedLocations) {
            $this->assertNotEmpty($case['title']);
            $this->assertContains($case['location'], $allowedLocations);
            $this->assertNotEmpty($case['categories']);
            $this->assertNotEmpty($case['services']);
            $this->assertNotEmpty($case['images']);
            $this->assertNotEmpty($case['cta']);

            collect($case['images'])->each(function ($image) {
                $this->assertStringStartsWith('/images/real-works/', $image['jpg']);
                $this->assertStringEndsWith('.webp', $image['webp']);
                $this->assertGreaterThan(0, $image['width']);
                $this->assertGreaterThan(0, $image['height']);
                $this->assertNotEmpty($image['alt']);
            });
        });
    }

    public function testRealWorkPreviewContextsOnlyReturnConfiguredCases()
    {
        $this->assertCount(4, RealWorks::featured('home'));
        $this->assertCount(2, RealWorks::featured('vkhidni-dveri-lutsk'));
        $this->assertCount(3, RealWorks::featured('mizhkimnatni-dveri-lutsk'));
        $this->assertCount(3, RealWorks::featured('dveri-volyn'));
        $this->assertTrue(RealWorks::featured('unsupported-context')->isEmpty());
    }
}
```

- [ ] **Step 2: Run the tests and verify RED**

Run:

```powershell
docker compose exec -T app php artisan test --filter RealWorksTest
```

Expected: FAIL because `App\Support\RealWorks` does not exist.

- [ ] **Step 3: Add the complete support API**

Create `app/Support/RealWorks.php`:

```php
<?php

namespace App\Support;

use Illuminate\Support\Collection;

class RealWorks
{
    public static function page(): array
    {
        return config('real_works.page', []);
    }

    public static function filters(): array
    {
        return config('real_works.filters', []);
    }

    public static function cases(): Collection
    {
        return collect(config('real_works.cases', []));
    }

    public static function videos(): Collection
    {
        return collect(config('real_works.videos', []));
    }

    public static function featured(string $context): Collection
    {
        $ids = config("real_works.previews.{$context}", []);

        return collect($ids)
            ->map(function ($id) {
                return self::cases()->firstWhere('id', $id);
            })
            ->filter()
            ->values();
    }

    public static function allImages(): Collection
    {
        return self::cases()
            ->flatMap(function ($case) {
                return $case['images'];
            })
            ->values();
    }
}
```

Create `config/real_works.php` with this exact top-level structure and complete records:

```php
<?php

return [
    'page' => [
        'path' => '/nashi-roboty',
        'title' => 'Реальні встановлення дверей у Луцьку та Волині | Метр на Метр',
        'description' => 'Приклади встановлених вхідних та міжкімнатних дверей у Луцьку й Волинській області. Замір, підбір, доставка та монтаж від Метр на Метр.',
        'h1' => 'Реальні встановлення дверей у Луцьку та Волині',
        'intro' => 'Показуємо реальні фото дверей після монтажу у квартирах і приватних будинках. Без вигаданих адрес і постановочних кейсів.',
        'og_image' => '/images/real-works/real-installations-og.jpg',
    ],
    'filters' => [
        ['id' => 'all', 'label' => 'Усі роботи'],
        ['id' => 'entrance', 'label' => 'Вхідні двері'],
        ['id' => 'interior', 'label' => 'Міжкімнатні двері'],
        ['id' => 'installation', 'label' => 'Двері з монтажем'],
        ['id' => 'lutsk', 'label' => 'Луцьк'],
        ['id' => 'volyn', 'label' => 'Волинь'],
    ],
    'cases' => [
        [
            'id' => 'white-black-modern',
            'title' => 'Білі міжкімнатні двері з чорними вставками у сучасному інтер’єрі',
            'type' => 'Міжкімнатні двері',
            'location' => 'Луцьк',
            'description' => 'Світлі полотна з чорними вставками та фурнітурою підтримують стриманий сучасний інтер’єр. На фото — готовий результат після встановлення.',
            'services' => ['підбір', 'замір', 'доставка', 'монтаж'],
            'categories' => ['interior', 'installation', 'lutsk', 'volyn'],
            'cta' => 'Запитати ціну таких дверей',
            'images' => [
                [
                    'jpg' => '/images/real-works/white-black-interior-main.jpg',
                    'webp' => '/images/real-works/white-black-interior-main.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Міжкімнатні двері з чорними вставками у сучасному інтер’єрі | Метр на Метр',
                ],
                [
                    'jpg' => '/images/real-works/white-black-interior-pair.jpg',
                    'webp' => '/images/real-works/white-black-interior-pair.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Білі міжкімнатні двері після монтажу у Луцьку | Метр на Метр',
                ],
            ],
        ],
        [
            'id' => 'light-apartment',
            'title' => 'Світлі міжкімнатні двері для квартири у Луцьку',
            'type' => 'Міжкімнатні двері',
            'location' => 'для квартири',
            'description' => 'Світлі двері встановлено в готовому інтер’єрі квартири. Чорна фурнітура створює акуратний контраст зі стінами та підлогою.',
            'services' => ['підбір', 'замір', 'доставка', 'монтаж'],
            'categories' => ['interior', 'installation', 'lutsk', 'volyn'],
            'cta' => 'Підібрати такі двері',
            'images' => [
                [
                    'jpg' => '/images/real-works/light-apartment-main.jpg',
                    'webp' => '/images/real-works/light-apartment-main.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Світлі міжкімнатні двері після монтажу у квартирі в Луцьку | Метр на Метр',
                ],
                [
                    'jpg' => '/images/real-works/light-apartment-angle.jpg',
                    'webp' => '/images/real-works/light-apartment-angle.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Світлі двері з чорною фурнітурою в інтер’єрі квартири | Метр на Метр',
                ],
            ],
        ],
        [
            'id' => 'mirror-entrance-house',
            'title' => 'Вхідні двері з дзеркалом для приватного будинку',
            'type' => 'Вхідні двері',
            'location' => 'Волинська область',
            'description' => 'Вхідні двері з вертикальним дзеркалом встановлено у готовий отвір. Окремі кадри показують полотно та встановлену фурнітуру.',
            'services' => ['підбір', 'замір', 'доставка', 'монтаж'],
            'categories' => ['entrance', 'installation', 'volyn'],
            'cta' => 'Запитати ціну',
            'images' => [
                [
                    'jpg' => '/images/real-works/mirror-entrance-main.jpg',
                    'webp' => '/images/real-works/mirror-entrance-main.webp',
                    'width' => 960,
                    'height' => 1275,
                    'alt' => 'Вхідні двері з дзеркалом після встановлення у Волинській області | Метр на Метр',
                ],
                [
                    'jpg' => '/images/real-works/mirror-entrance-hardware.jpg',
                    'webp' => '/images/real-works/mirror-entrance-hardware.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Фурнітура вхідних дверей після монтажу | Метр на Метр',
                ],
            ],
        ],
        [
            'id' => 'interior-fittings',
            'title' => 'Міжкімнатні двері з акуратним монтажем та фурнітурою',
            'type' => 'Міжкімнатні двері',
            'location' => 'Луцьк',
            'description' => 'Дверне полотно, лиштва та чорна фурнітура зібрані в одному стилі. Відкритий ракурс показує готовий монтаж у кімнаті.',
            'services' => ['замір', 'доставка', 'монтаж'],
            'categories' => ['interior', 'installation', 'lutsk', 'volyn'],
            'cta' => 'Замовити замір',
            'images' => [
                [
                    'jpg' => '/images/real-works/interior-fittings-main.jpg',
                    'webp' => '/images/real-works/interior-fittings-main.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Міжкімнатні двері з чорною фурнітурою після монтажу у Луцьку | Метр на Метр',
                ],
                [
                    'jpg' => '/images/real-works/interior-fittings-open.jpg',
                    'webp' => '/images/real-works/interior-fittings-open.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Відкриті міжкімнатні двері після встановлення | Метр на Метр',
                ],
            ],
        ],
        [
            'id' => 'installed-entrance-examples',
            'title' => 'Приклади встановлених вхідних дверей від Метр на Метр',
            'type' => 'Вхідні двері',
            'location' => 'Волинська область',
            'description' => 'Два окремі приклади вхідних дверей після встановлення: для квартири та для приватного будинку. Точні адреси клієнтів не публікуємо.',
            'services' => ['підбір', 'замір', 'доставка', 'монтаж'],
            'categories' => ['entrance', 'installation', 'volyn'],
            'cta' => 'Підібрати вхідні двері',
            'images' => [
                [
                    'jpg' => '/images/real-works/entrance-apartment-main.jpg',
                    'webp' => '/images/real-works/entrance-apartment-main.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Вхідні двері після монтажу для квартири | Метр на Метр',
                ],
                [
                    'jpg' => '/images/real-works/entrance-house-main.jpg',
                    'webp' => '/images/real-works/entrance-house-main.webp',
                    'width' => 960,
                    'height' => 1280,
                    'alt' => 'Темні вхідні двері після встановлення у приватному будинку | Метр на Метр',
                ],
            ],
        ],
    ],
    'videos' => [
        [
            'id' => 'door-overview',
            'title' => 'Відеоогляд дверей та оздоблення',
            'src' => '/images/real-works/door-overview.mp4',
            'poster_jpg' => '/images/real-works/door-overview-poster.jpg',
            'poster_webp' => '/images/real-works/door-overview-poster.webp',
            'width' => 1072,
            'height' => 1904,
        ],
    ],
    'previews' => [
        'home' => ['white-black-modern', 'mirror-entrance-house', 'light-apartment', 'installed-entrance-examples'],
        'vkhidni-dveri-lutsk' => ['mirror-entrance-house', 'installed-entrance-examples'],
        'mizhkimnatni-dveri-lutsk' => ['white-black-modern', 'light-apartment', 'interior-fittings'],
        'dveri-volyn' => ['mirror-entrance-house', 'installed-entrance-examples', 'white-black-modern'],
    ],
];
```

- [ ] **Step 4: Run the content tests and verify GREEN**

Run:

```powershell
docker compose exec -T app php artisan test --filter RealWorksTest
```

Expected: 2 tests pass.

- [ ] **Step 5: Commit the content contract**

```powershell
git add -- config/real_works.php app/Support/RealWorks.php tests/Feature/RealWorksTest.php
git diff --cached --check
git commit -m "feat: define verified door installation cases"
```

---

### Task 2: Prepare optimized photo, poster, OG, and deferred video assets

**Files:**

- Modify: `tests/Feature/RealWorksTest.php`
- Create: `resources/client/images/real-works/*.jpg`
- Create: `resources/client/images/real-works/*.webp`
- Create: `resources/client/images/real-works/door-overview.mp4`

**Interfaces:**

- Consumes: `RealWorks::allImages(): Collection`
- Consumes: `RealWorks::videos(): Collection`
- Produces: every asset path declared in `config/real_works.php`

- [ ] **Step 1: Add the failing asset-integrity tests**

Append to `tests/Feature/RealWorksTest.php`:

```php
public function testEveryConfiguredRealWorkAssetExistsAndMatchesDimensions()
{
    RealWorks::allImages()->each(function ($image) {
        $jpg = resource_path('client' . $image['jpg']);
        $webp = resource_path('client' . $image['webp']);

        $this->assertFileExists($jpg);
        $this->assertFileExists($webp);
        $this->assertSame([$image['width'], $image['height']], array_slice(getimagesize($jpg), 0, 2));
        $this->assertSame([$image['width'], $image['height']], array_slice(getimagesize($webp), 0, 2));
        $this->assertLessThan(350 * 1024, filesize($jpg));
        $this->assertLessThan(250 * 1024, filesize($webp));
    });
}

public function testOnlyTheApprovedDoorVideoAndItsPostersArePublished()
{
    $this->assertCount(1, RealWorks::videos());

    RealWorks::videos()->each(function ($video) {
        $this->assertFileExists(resource_path('client' . $video['src']));
        $this->assertFileExists(resource_path('client' . $video['poster_jpg']));
        $this->assertFileExists(resource_path('client' . $video['poster_webp']));
    });

    $this->assertFileDoesNotExist(resource_path('client/images/real-works/video-9.mp4'));
}
```

- [ ] **Step 2: Run the asset tests and verify RED**

Run:

```powershell
docker compose exec -T app php artisan test --filter "EveryConfiguredRealWorkAsset|OnlyTheApprovedDoorVideo"
```

Expected: FAIL because the optimized assets do not exist.

- [ ] **Step 3: Generate the approved images with Pillow**

Run this PowerShell block from the repository root:

```powershell
$env:MM_MEDIA_SOURCE = 'D:\Project\02_Web проєкти\Клієнтські сайти та матеріали\metrnametr.com\new content'
$env:MM_MEDIA_OUTPUT = (Join-Path $PWD 'resources\client\images\real-works')
New-Item -ItemType Directory -Path $env:MM_MEDIA_OUTPUT -Force | Out-Null
@'
from pathlib import Path
from PIL import Image, ImageOps
import os

source = Path(os.environ["MM_MEDIA_SOURCE"])
output = Path(os.environ["MM_MEDIA_OUTPUT"])
mapping = {
    "white-black-interior-main": "photo_2026-07-23_12-59-29.jpg",
    "white-black-interior-pair": "photo_2026-07-23_12-59-31.jpg",
    "light-apartment-main": "photo_2026-07-23_12-59-21.jpg",
    "light-apartment-angle": "photo_2026-07-23_12-59-25.jpg",
    "mirror-entrance-main": "photo_2026-07-23_12-58-59.jpg",
    "mirror-entrance-hardware": "photo_2026-07-23_12-58-57.jpg",
    "interior-fittings-main": "photo_2026-07-23_13-00-24.jpg",
    "interior-fittings-open": "photo_2026-07-23_13-00-30.jpg",
    "entrance-apartment-main": "photo_2026-07-23_13-00-14.jpg",
    "entrance-house-main": "photo_2026-07-23_13-00-18.jpg",
}

def optimized(name, file_name):
    image = ImageOps.exif_transpose(Image.open(source / file_name)).convert("RGB")
    if image.width > 960:
        height = round(image.height * 960 / image.width)
        image = image.resize((960, height), Image.Resampling.LANCZOS)
    image.save(output / f"{name}.jpg", "JPEG", quality=82, optimize=True, progressive=True)
    image.save(output / f"{name}.webp", "WEBP", quality=78, method=6)

for target, source_name in mapping.items():
    optimized(target, source_name)

left = Image.open(output / "white-black-interior-main.jpg").convert("RGB")
right = Image.open(output / "entrance-house-main.jpg").convert("RGB")
canvas = Image.new("RGB", (1200, 630), "white")
for image, box in ((left, (0, 0, 600, 630)), (right, (600, 0, 1200, 630))):
    canvas.paste(ImageOps.fit(image, (box[2] - box[0], box[3] - box[1]), Image.Resampling.LANCZOS), box)
canvas.save(output / "real-installations-og.jpg", "JPEG", quality=84, optimize=True, progressive=True)
canvas.save(output / "real-installations-og.webp", "WEBP", quality=80, method=6)
'@ | python -
```

Expected: ten JPEG/WebP pairs plus `real-installations-og.jpg` and `.webp` are created.

- [ ] **Step 4: Extract the poster and copy only the approved H.264 video**

Run:

```powershell
$env:MM_APPROVED_VIDEO = 'D:\Project\02_Web проєкти\Клієнтські сайти та матеріали\metrnametr.com\new content\IMG_3157.MP4'
$env:MM_MEDIA_OUTPUT = (Join-Path $PWD 'resources\client\images\real-works')
@'
from pathlib import Path
from PIL import Image
import cv2
import os
import shutil

source = Path(os.environ["MM_APPROVED_VIDEO"])
output = Path(os.environ["MM_MEDIA_OUTPUT"])
capture = cv2.VideoCapture(str(source))
frame_count = int(capture.get(cv2.CAP_PROP_FRAME_COUNT))
capture.set(cv2.CAP_PROP_POS_FRAMES, round(frame_count * 0.9))
ok, frame = capture.read()
capture.release()
if not ok:
    raise RuntimeError("Could not extract approved video poster")
frame = cv2.cvtColor(frame, cv2.COLOR_BGR2RGB)
poster = Image.fromarray(frame)
poster.save(output / "door-overview-poster.jpg", "JPEG", quality=82, optimize=True, progressive=True)
poster.save(output / "door-overview-poster.webp", "WEBP", quality=78, method=6)
shutil.copy2(source, output / "door-overview.mp4")
'@ | python -
```

Expected: two poster formats and `door-overview.mp4` exist; no file is created from `video (9).mp4`.

- [ ] **Step 5: Run asset tests and verify GREEN**

Run:

```powershell
docker compose exec -T app php artisan test --filter RealWorksTest
```

Expected: all content and asset tests pass.

- [ ] **Step 6: Inspect the generated assets**

Run:

```powershell
Get-ChildItem resources\client\images\real-works | Sort-Object Name | Select-Object Name,Length
```

Use `view_image` on:

- `resources/client/images/real-works/white-black-interior-main.webp`
- `resources/client/images/real-works/mirror-entrance-main.webp`
- `resources/client/images/real-works/real-installations-og.jpg`
- `resources/client/images/real-works/door-overview-poster.webp`

Expected: correct orientation, no unrelated frames, no showroom/collage image, no new logo, and no visibly destructive compression.

- [ ] **Step 7: Commit optimized media**

```powershell
git add -- resources/client/images/real-works tests/Feature/RealWorksTest.php
git diff --cached --check
git commit -m "feat: add optimized real installation media"
```

---

### Task 3: Build the canonical works page and structured data

**Files:**

- Create: `app/Http/Controllers/RealWorksController.php`
- Create: `resources/views/client/works/index.blade.php`
- Create: `resources/views/client/works/partials/picture.blade.php`
- Create: `resources/views/client/works/partials/case.blade.php`
- Create: `resources/views/client/works/partials/video.blade.php`
- Modify: `routes/web.php`
- Modify: `app/Support/SeoContent.php`
- Modify: `tests/Feature/RealWorksTest.php`

**Interfaces:**

- Consumes: every `RealWorks` accessor from Task 1.
- Produces: route name `real-works.index`.
- Produces: `SeoContent::realWorksGallerySchema(Collection $cases): array`.
- Produces DOM hooks: `[data-work-filter]`, `[data-work-case]`, `[data-work-image]`, `[data-work-video]`.

- [ ] **Step 1: Add failing page and schema tests**

Append:

```php
public function testRealWorksPageHasExactMetadataCanonicalAndContent()
{
    $response = $this->get('/nashi-roboty')
        ->assertStatus(200)
        ->assertSee('<title>Реальні встановлення дверей у Луцьку та Волині | Метр на Метр</title>', false)
        ->assertSee('name="description" content="Приклади встановлених вхідних та міжкімнатних дверей у Луцьку й Волинській області. Замір, підбір, доставка та монтаж від Метр на Метр."', false)
        ->assertSee('<link rel="canonical" href="https://metrnametr.com.ua/nashi-roboty"', false)
        ->assertSee('<h1>Реальні встановлення дверей у Луцьку та Волині</h1>', false)
        ->assertSee('og:image', false)
        ->assertSee('https://metrnametr.com.ua/images/real-works/real-installations-og.jpg', false)
        ->assertSee('class="real-work-case"', false)
        ->assertSee('Відео з реальних робіт')
        ->assertDontSee('video (9).mp4', false);

    $this->assertSame(5, substr_count($response->getContent(), 'class="real-work-case"'));
}

public function testRealWorksPageEmitsBreadcrumbAndImageGallerySchema()
{
    $content = $this->get('/nashi-roboty')->assertStatus(200)->getContent();

    $this->assertStringContainsString('"@type": "BreadcrumbList"', $content);
    $this->assertStringContainsString('"@type": "ImageGallery"', $content);
    $this->assertStringContainsString('"@type": "ImageObject"', $content);
    $this->assertStringContainsString('"contentUrl": "https://metrnametr.com.ua/images/real-works/', $content);
}

public function testRealWorksPicturesHaveFallbackDimensionsAndDeferredLoading()
{
    $content = $this->get('/nashi-roboty')->assertStatus(200)->getContent();

    $this->assertStringContainsString('<source type="image/webp"', $content);
    $this->assertStringContainsString('width="960"', $content);
    $this->assertStringContainsString('height="1280"', $content);
    $this->assertStringContainsString('loading="lazy"', $content);
    $this->assertStringContainsString('decoding="async"', $content);
    $this->assertStringContainsString('data-video-src="/images/real-works/door-overview.mp4"', $content);
    $this->assertStringNotContainsString('<video src=', $content);
    $this->assertStringNotContainsString('<source src="/images/real-works/door-overview.mp4"', $content);
}
```

- [ ] **Step 2: Run the page tests and verify RED**

Run:

```powershell
docker compose exec -T app php artisan test --filter "RealWorksPage"
```

Expected: FAIL with HTTP 404.

- [ ] **Step 3: Add route, controller, and schema**

Add to `routes/web.php` before parameterized knowledge routes:

```php
Route::get('/nashi-roboty', ['as' => 'real-works.index', 'uses' => 'RealWorksController@index']);
```

Create `app/Http/Controllers/RealWorksController.php`:

```php
<?php

namespace App\Http\Controllers;

use App\Support\RealWorks;
use App\Support\SeoContent;

class RealWorksController extends Controller
{
    public function index()
    {
        $page = RealWorks::page();
        $cases = RealWorks::cases();

        return view('client.works.index', [
            'page' => $page,
            'cases' => $cases,
            'filters' => RealWorks::filters(),
            'videos' => RealWorks::videos(),
            'title' => $page['title'],
            'description' => $page['description'],
            'canonical' => SeoContent::canonical($page['path']),
            'ogImage' => SeoContent::canonical($page['og_image']),
            'breadcrumbs' => [$page['path'] => $page['h1']],
            'schema' => [
                SeoContent::breadcrumbSchema([
                    '/' => 'Головна',
                    $page['path'] => $page['h1'],
                ]),
                SeoContent::realWorksGallerySchema($cases),
            ],
        ]);
    }
}
```

Add to `SeoContent`:

```php
public static function realWorksGallerySchema($cases)
{
    $images = collect($cases)->flatMap(function ($case) {
        return collect($case['images'])->map(function ($image) use ($case) {
            return [
                '@type' => 'ImageObject',
                'name' => $case['title'],
                'caption' => $image['alt'],
                'contentUrl' => self::canonical($image['jpg']),
                'thumbnailUrl' => self::canonical($image['webp']),
                'width' => $image['width'],
                'height' => $image['height'],
            ];
        });
    })->values()->all();

    return [
        '@context' => 'https://schema.org',
        '@type' => 'ImageGallery',
        'name' => 'Реальні встановлення дверей у Луцьку та Волині',
        'description' => RealWorks::page()['description'],
        'url' => self::canonical(RealWorks::page()['path']),
        'associatedMedia' => $images,
    ];
}
```

Add `use App\Support\RealWorks;` only if the method is moved outside the existing `App\Support` namespace; inside `SeoContent` both classes share the namespace and no import is necessary.

- [ ] **Step 4: Create the picture and case partials**

Create `resources/views/client/works/partials/picture.blade.php`:

```blade
<picture>
    <source type="image/webp" srcset="{{ $image['webp'] }}">
    <img
        src="{{ $image['jpg'] }}"
        width="{{ $image['width'] }}"
        height="{{ $image['height'] }}"
        alt="{{ $image['alt'] }}"
        decoding="async"
        @if(!empty($priority)) fetchpriority="high" @endif
        @if(!empty($lazy)) loading="lazy" @endif
        @if(!empty($lightbox))
            data-work-image
            data-work-case-id="{{ $caseId }}"
            data-work-image-index="{{ $imageIndex }}"
        @endif
    >
</picture>
```

Create `resources/views/client/works/partials/case.blade.php`:

```blade
<article class="real-work-case" data-work-case data-work-tags="{{ implode(' ', $case['categories']) }}">
    <div class="real-work-case__media">
        @foreach($case['images'] as $image)
            <button
                class="real-work-case__image"
                type="button"
                aria-label="Збільшити фото: {{ $image['alt'] }}"
            >
                @include('client.works.partials.picture', [
                    'image' => $image,
                    'priority' => false,
                    'lazy' => true,
                    'lightbox' => true,
                    'caseId' => $case['id'],
                    'imageIndex' => $loop->index,
                ])
            </button>
        @endforeach
    </div>
    <div class="real-work-case__content">
        <p class="real-work-case__meta">{{ $case['type'] }} · {{ $case['location'] }}</p>
        <h2>{{ $case['title'] }}</h2>
        <p>{{ $case['description'] }}</p>
        <p class="real-work-case__services"><strong>Що зроблено:</strong> {{ implode(' / ', $case['services']) }}</p>
        <a
            class="yellow-btn blue-hover"
            href="#order-form"
            data-mobile-order-cta
            data-ga-event="ask_price_click"
            data-cta-location="real_work_{{ $case['id'] }}"
        >{{ $case['cta'] }}</a>
    </div>
</article>
```

Create `resources/views/client/works/partials/video.blade.php`:

```blade
<article class="real-work-video">
    <button
        type="button"
        class="real-work-video__trigger"
        data-work-video
        data-video-src="{{ $video['src'] }}"
        aria-label="Відтворити: {{ $video['title'] }}"
    >
        <picture>
            <source type="image/webp" srcset="{{ $video['poster_webp'] }}">
            <img
                src="{{ $video['poster_jpg'] }}"
                width="{{ $video['width'] }}"
                height="{{ $video['height'] }}"
                alt="{{ $video['title'] }}"
                loading="lazy"
                decoding="async"
            >
        </picture>
        <span class="real-work-video__play" aria-hidden="true">▶</span>
    </button>
    <h3>{{ $video['title'] }}</h3>
</article>
```

- [ ] **Step 5: Create the complete page**

Create `resources/views/client/works/index.blade.php`:

```blade
@extends('client.layouts.main')

@section('content')
    @include('client.shared.breadcrumb', ['breadcrumbs' => $breadcrumbs])

    <main class="real-works-page" data-real-works-root>
        <section class="real-works-intro">
            <div class="container">
                <div class="real-works-intro__grid">
                    <div class="real-works-intro__copy">
                        <h1>{{ $page['h1'] }}</h1>
                        <p>{{ $page['intro'] }}</p>
                    </div>
                    <div class="real-works-intro__media">
                        @include('client.works.partials.picture', [
                            'image' => $cases[0]['images'][0],
                            'priority' => true,
                            'lazy' => false,
                            'lightbox' => false,
                        ])
                        @include('client.works.partials.picture', [
                            'image' => $cases[4]['images'][1],
                            'priority' => false,
                            'lazy' => false,
                            'lightbox' => false,
                        ])
                    </div>
                </div>
            </div>
        </section>

        <nav class="real-work-filters container" aria-label="Фільтри виконаних робіт">
            @foreach($filters as $filter)
                <button
                    type="button"
                    data-work-filter="{{ $filter['id'] }}"
                    aria-pressed="{{ $loop->first ? 'true' : 'false' }}"
                >{{ $filter['label'] }}</button>
            @endforeach
        </nav>

        <section class="real-work-list container" aria-label="Приклади встановлених дверей">
            @foreach($cases as $case)
                @include('client.works.partials.case', ['case' => $case])
            @endforeach
        </section>

        <section class="real-work-videos" aria-labelledby="real-work-videos-title">
            <div class="container">
                <h2 id="real-work-videos-title">Відео з реальних робіт</h2>
                <p>Відео завантажується лише після натискання.</p>
                <div class="real-work-videos__grid">
                    @foreach($videos as $video)
                        @include('client.works.partials.video', ['video' => $video])
                    @endforeach
                </div>
            </div>
        </section>

        <section class="real-works-cta">
            <div class="container">
                <h2>Потрібні двері з монтажем?</h2>
                <p>Допоможемо підібрати модель, виконати замір, організувати доставку та встановлення у Луцьку або Волинській області.</p>
                <a class="yellow-btn blue-hover" href="#order-form" data-mobile-order-cta data-ga-event="ask_price_click" data-cta-location="real_works_footer">Замовити замір</a>
            </div>
        </section>
    </main>

    <div class="real-work-lightbox" data-work-lightbox hidden role="dialog" aria-modal="true" aria-label="Збільшений перегляд фото">
        <button type="button" data-work-lightbox-close aria-label="Закрити">×</button>
        <button type="button" data-work-lightbox-prev aria-label="Попереднє фото">‹</button>
        <figure>
            <img data-work-lightbox-image src="" alt="">
            <figcaption data-work-lightbox-caption></figcaption>
        </figure>
        <button type="button" data-work-lightbox-next aria-label="Наступне фото">›</button>
    </div>

    @include('client.products.shared.modal')
@endsection
```

- [ ] **Step 6: Run page tests and verify GREEN**

Run:

```powershell
docker compose exec -T app php artisan test --filter RealWorksTest
```

Expected: all current `RealWorksTest` tests pass.

- [ ] **Step 7: Commit the page and schema**

```powershell
git add -- routes/web.php app/Http/Controllers/RealWorksController.php app/Support/SeoContent.php resources/views/client/works tests/Feature/RealWorksTest.php
git diff --cached --check
git commit -m "feat: add real door installations page"
```

---

### Task 4: Add accessible filters, lightbox, deferred video, and responsive styling

**Files:**

- Create: `resources/client/js/real-works.js`
- Create: `tests/js/real-works.test.js`
- Create: `resources/client/scss/_real-works.scss`
- Modify: `resources/client/js/app.js`
- Modify: `resources/client/scss/app.scss`
- Modify: `package.json`

**Interfaces:**

- Produces: `matchesWorkFilter(tags: string[], filter: string): boolean`
- Produces: `nextWorkImageIndex(current: number, total: number, direction: number): number`
- Consumes DOM hooks from Task 3.

- [ ] **Step 1: Add `npm test` and write failing pure-function tests**

Add to `package.json` scripts:

```json
"test": "node --test tests/js/*.test.js"
```

Create `tests/js/real-works.test.js`:

```js
const test = require('node:test');
const assert = require('node:assert/strict');
const {
  matchesWorkFilter,
  nextWorkImageIndex,
} = require('../../resources/client/js/real-works');

test('all filter keeps every case visible', () => {
  assert.equal(matchesWorkFilter(['interior', 'lutsk'], 'all'), true);
});

test('category filter only matches declared case tags', () => {
  assert.equal(matchesWorkFilter(['interior', 'lutsk'], 'interior'), true);
  assert.equal(matchesWorkFilter(['interior', 'lutsk'], 'entrance'), false);
});

test('lightbox index wraps in both directions', () => {
  assert.equal(nextWorkImageIndex(1, 2, 1), 0);
  assert.equal(nextWorkImageIndex(0, 2, -1), 1);
});
```

- [ ] **Step 2: Run JavaScript tests and verify RED**

Run:

```powershell
npm test
```

Expected: FAIL because `resources/client/js/real-works.js` does not exist.

- [ ] **Step 3: Implement pure functions and browser initialization**

Create `resources/client/js/real-works.js` with:

```js
function matchesWorkFilter(tags, filter) {
  return filter === 'all' || tags.indexOf(filter) !== -1;
}

function nextWorkImageIndex(current, total, direction) {
  return (current + direction + total) % total;
}

function initRealWorks() {
  var root = document.querySelector('[data-real-works-root]');
  if (!root) return;

  var filterButtons = Array.prototype.slice.call(root.querySelectorAll('[data-work-filter]'));
  var cases = Array.prototype.slice.call(root.querySelectorAll('[data-work-case]'));
  filterButtons.forEach(function (button) {
    button.addEventListener('click', function () {
      var filter = button.getAttribute('data-work-filter');
      filterButtons.forEach(function (candidate) {
        candidate.setAttribute('aria-pressed', candidate === button ? 'true' : 'false');
      });
      cases.forEach(function (item) {
        var tags = (item.getAttribute('data-work-tags') || '').split(/\s+/).filter(Boolean);
        item.hidden = !matchesWorkFilter(tags, filter);
      });
    });
  });

  var lightbox = document.querySelector('[data-work-lightbox]');
  var imageButtons = Array.prototype.slice.call(root.querySelectorAll('[data-work-image]'));
  var activeIndex = 0;
  var returnFocus = null;

  function renderLightbox() {
    var source = imageButtons[activeIndex];
    var image = lightbox.querySelector('[data-work-lightbox-image]');
    image.src = source.currentSrc || source.src;
    image.alt = source.alt;
    lightbox.querySelector('[data-work-lightbox-caption]').textContent = source.alt;
  }

  function openLightbox(index) {
    activeIndex = index;
    returnFocus = imageButtons[index].closest('button');
    renderLightbox();
    lightbox.hidden = false;
    document.body.classList.add('has-open-lightbox');
    lightbox.querySelector('[data-work-lightbox-close]').focus();
  }

  function closeLightbox() {
    lightbox.hidden = true;
    document.body.classList.remove('has-open-lightbox');
    if (returnFocus) returnFocus.focus();
  }

  imageButtons.forEach(function (image, index) {
    image.closest('button').addEventListener('click', function () {
      openLightbox(index);
    });
  });

  lightbox.querySelector('[data-work-lightbox-close]').addEventListener('click', closeLightbox);
  lightbox.querySelector('[data-work-lightbox-prev]').addEventListener('click', function () {
    activeIndex = nextWorkImageIndex(activeIndex, imageButtons.length, -1);
    renderLightbox();
  });
  lightbox.querySelector('[data-work-lightbox-next]').addEventListener('click', function () {
    activeIndex = nextWorkImageIndex(activeIndex, imageButtons.length, 1);
    renderLightbox();
  });
  lightbox.addEventListener('click', function (event) {
    if (event.target === lightbox) closeLightbox();
  });
  document.addEventListener('keydown', function (event) {
    if (lightbox.hidden) return;
    if (event.key === 'Escape') closeLightbox();
    if (event.key === 'ArrowLeft') lightbox.querySelector('[data-work-lightbox-prev]').click();
    if (event.key === 'ArrowRight') lightbox.querySelector('[data-work-lightbox-next]').click();
    if (event.key === 'Tab') {
      var focusable = Array.prototype.slice.call(lightbox.querySelectorAll('button:not([disabled])'));
      var first = focusable[0];
      var last = focusable[focusable.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });

  Array.prototype.slice.call(root.querySelectorAll('[data-work-video]')).forEach(function (trigger) {
    trigger.addEventListener('click', function () {
      var video = document.createElement('video');
      video.controls = true;
      video.autoplay = true;
      video.preload = 'metadata';
      video.poster = trigger.querySelector('img').currentSrc || trigger.querySelector('img').src;
      video.src = trigger.getAttribute('data-video-src');
      trigger.replaceWith(video);
    }, { once: true });
  });
}

if (typeof document !== 'undefined') {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initRealWorks);
  } else {
    initRealWorks();
  }
}

module.exports = {
  matchesWorkFilter: matchesWorkFilter,
  nextWorkImageIndex: nextWorkImageIndex,
  initRealWorks: initRealWorks,
};
```

Add near the existing requires in `resources/client/js/app.js`:

```js
require('./real-works');
```

- [ ] **Step 4: Run JavaScript tests and verify GREEN**

Run:

```powershell
npm test
```

Expected: 3 tests pass.

- [ ] **Step 5: Add the real-work design system styles**

Create `resources/client/scss/_real-works.scss` with these required selectors and values:

```scss
.real-works-page {
  --works-ink: #17191e;
  --works-muted: #626873;
  --works-line: #e5e7eb;
  --works-surface: #f7f8fa;
  color: var(--works-ink);
}

.real-works-intro {
  padding: 72px 0 56px;
  background: #fff;

  &__grid {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(320px, .85fr);
    gap: 56px;
    align-items: center;
  }

  h1 {
    margin: 0 0 20px;
    font-size: clamp(36px, 5vw, 58px);
    line-height: 1.08;
  }

  p {
    max-width: 680px;
    color: var(--works-muted);
    font-size: 18px;
    line-height: 1.65;
  }

  &__media {
    display: grid;
    grid-template-columns: 1.1fr .9fr;
    gap: 12px;
    align-items: end;
  }

  img {
    display: block;
    width: 100%;
    height: auto;
    max-height: 440px;
    object-fit: cover;
  }
}

.real-work-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  padding-top: 22px;
  padding-bottom: 22px;
  border-top: 1px solid var(--works-line);
  border-bottom: 1px solid var(--works-line);

  button {
    min-height: 44px;
    padding: 10px 16px;
    border: 1px solid #cfd3da;
    border-radius: 999px;
    background: #fff;
    color: #333944;
    font: inherit;
    font-weight: 600;
  }

  button[aria-pressed='true'] {
    border-color: var(--works-ink);
    background: var(--works-ink);
    color: #fff;
  }
}

.real-work-case {
  display: grid;
  grid-template-columns: minmax(0, 1.08fr) minmax(300px, .92fr);
  gap: 52px;
  align-items: center;
  padding: 64px 0;
  border-bottom: 1px solid var(--works-line);

  &:nth-child(even) {
    .real-work-case__media { order: 2; }
  }

  &[hidden] { display: none; }

  &__media {
    display: grid;
    grid-template-columns: 1.2fr .8fr;
    gap: 12px;
    min-width: 0;
  }

  &__image {
    min-width: 0;
    padding: 0;
    border: 0;
    background: transparent;
    cursor: zoom-in;

    img {
      display: block;
      width: 100%;
      height: 430px;
      object-fit: cover;
    }
  }

  &__meta {
    color: #826b00;
    font-size: 13px;
    font-weight: 700;
    letter-spacing: .06em;
    text-transform: uppercase;
  }

  h2 {
    margin: 10px 0 16px;
    font-size: clamp(28px, 3vw, 40px);
    line-height: 1.16;
  }

  &__content > p:not(.real-work-case__meta) {
    color: var(--works-muted);
    line-height: 1.65;
  }
}

.real-work-videos {
  padding: 72px 0;
  background: #17191e;
  color: #fff;

  &__grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 420px));
    gap: 24px;
    margin-top: 28px;
  }
}

.real-work-video__trigger {
  position: relative;
  display: block;
  width: 100%;
  padding: 0;
  overflow: hidden;
  border: 0;
  background: #0e1014;

  img {
    display: block;
    width: 100%;
    max-height: 520px;
    object-fit: cover;
  }
}

.real-work-video video {
  display: block;
  width: 100%;
  max-height: 520px;
}

.real-work-video__play {
  position: absolute;
  top: 50%;
  left: 50%;
  display: grid;
  width: 58px;
  height: 58px;
  transform: translate(-50%, -50%);
  place-items: center;
  border: 1px solid rgba(255, 255, 255, .7);
  border-radius: 50%;
  background: rgba(0, 0, 0, .55);
  color: #fff;
}

.real-works-cta {
  padding: 72px 0;
  background: var(--works-surface);
  text-align: center;
}

.real-work-lightbox {
  position: fixed;
  z-index: 9999;
  inset: 0;
  display: grid;
  grid-template-columns: 64px minmax(0, 1fr) 64px;
  align-items: center;
  padding: 24px;
  background: rgba(8, 10, 14, .94);

  &[hidden] { display: none; }

  figure { margin: 0; text-align: center; }
  img { max-width: 100%; max-height: 82vh; }
  figcaption { margin-top: 12px; color: #fff; }
  button { min-width: 44px; min-height: 44px; }
  [data-work-lightbox-close] { position: absolute; top: 18px; right: 18px; }
}

.has-open-lightbox { overflow: hidden; }

@media (max-width: 767px) {
  .real-works-intro {
    padding: 44px 0 36px;

    &__grid { grid-template-columns: 1fr; gap: 28px; }
  }

  .real-work-case {
    grid-template-columns: 1fr;
    gap: 26px;
    padding: 44px 0;

    &:nth-child(even) .real-work-case__media { order: 0; }
    &__image img { height: auto; aspect-ratio: 3 / 4; }
  }

  .real-work-lightbox {
    grid-template-columns: 44px minmax(0, 1fr) 44px;
    padding: 12px;
  }
}

@media (max-width: 360px) {
  .real-work-case__media { grid-template-columns: 1fr; }
}

@media (prefers-reduced-motion: reduce) {
  .real-works-page *,
  .real-work-lightbox * {
    scroll-behavior: auto !important;
    transition: none !important;
  }
}
```

Add to `resources/client/scss/app.scss`:

```scss
@import "real-works";
```

- [ ] **Step 6: Build production assets**

Run:

```powershell
npm run production
```

Expected: exit 0 and updated client bundle entries in `public/mix-manifest.json`; no Sass or webpack errors.

- [ ] **Step 7: Commit interactions and styles**

```powershell
git add -- package.json resources/client/js/app.js resources/client/js/real-works.js resources/client/scss/app.scss resources/client/scss/_real-works.scss tests/js/real-works.test.js public/mix-manifest.json
git diff --cached --check
git commit -m "feat: add accessible works gallery interactions"
```

Do not stage ignored compiled assets under `public/assets` or `public/images`.

---

### Task 5: Add compact real-work proof to the homepage and local landings

**Files:**

- Create: `resources/views/client/shared/real-works-preview.blade.php`
- Modify: `app/Http/Controllers/MainController.php`
- Modify: `app/Http/Controllers/SeoController.php`
- Modify: `resources/views/client/main/index.blade.php`
- Modify: `resources/views/client/seo/landing.blade.php`
- Modify: `resources/client/scss/_real-works.scss`
- Modify: `tests/Feature/RealWorksTest.php`

**Interfaces:**

- Consumes: `RealWorks::featured(string $context): Collection`
- Produces view variable: `$realWorksPreview`
- Produces shared partial input: `works: Collection`, `context: string`

- [ ] **Step 1: Add failing preview integration tests**

Append:

```php
public function testHomepageShowsFourRealWorkPhotosAndWorksLink()
{
    $response = $this->get('/')
        ->assertStatus(200)
        ->assertSee('Реальні встановлення дверей')
        ->assertSee('/nashi-roboty', false)
        ->assertSee('data-real-works-preview="home"', false);

    $this->assertSame(4, substr_count($response->getContent(), 'data-preview-work-image'));
}

public function testThreeLocalLandingsShowRelevantRealWorkProof()
{
    $expected = [
        '/vkhidni-dveri-lutsk' => 2,
        '/mizhkimnatni-dveri-lutsk' => 3,
        '/dveri-volyn' => 3,
    ];

    foreach ($expected as $url => $count) {
        $response = $this->get($url)
            ->assertStatus(200)
            ->assertSee('Реальні встановлення дверей')
            ->assertSee('/nashi-roboty', false);

        $this->assertSame($count, substr_count($response->getContent(), 'data-preview-work-image'), $url);
    }
}
```

- [ ] **Step 2: Run preview tests and verify RED**

Run:

```powershell
docker compose exec -T app php artisan test --filter "HomepageShowsFourRealWork|ThreeLocalLandings"
```

Expected: FAIL because the blocks are absent.

- [ ] **Step 3: Pass preview data from controllers**

Add `use App\Support\RealWorks;` to `MainController` and extend its home view chain:

```php
->with('realWorksPreview', RealWorks::featured('home'))
```

Add `use App\Support\RealWorks;` to `SeoController` and extend `landing()`:

```php
->with('realWorksPreview', RealWorks::featured($slug))
```

- [ ] **Step 4: Create the compact shared preview**

Create `resources/views/client/shared/real-works-preview.blade.php`:

```blade
@if(!empty($works) && $works->isNotEmpty())
    <section class="real-works-preview" data-real-works-preview="{{ $context }}" aria-labelledby="real-works-preview-title-{{ $context }}">
        <div class="container">
            <div class="real-works-preview__heading">
                <div>
                    <h2 id="real-works-preview-title-{{ $context }}">Реальні встановлення дверей</h2>
                    <p>Фото дверей після монтажу у квартирах і приватних будинках Луцька та Волинської області.</p>
                </div>
                <a class="yellow-btn blue-hover" href="{{ route('real-works.index') }}">Переглянути роботи</a>
            </div>
            <div class="real-works-preview__grid">
                @foreach($works as $case)
                    <a href="{{ route('real-works.index') }}#{{ $case['id'] }}" data-preview-work-image>
                        @include('client.works.partials.picture', [
                            'image' => $case['images'][0],
                            'priority' => false,
                            'lazy' => true,
                            'lightbox' => false,
                        ])
                        <span>{{ $case['title'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </section>
@endif
```

Add `id="{{ $case['id'] }}"` to the case `<article>` in `case.blade.php`.

- [ ] **Step 5: Place the shared preview**

In `resources/views/client/main/index.blade.php`, insert after `</section>` for `.home-trust-section` and before `.news-box`:

```blade
@include('client.shared.real-works-preview', [
    'works' => $realWorksPreview,
    'context' => 'home',
])
```

In `resources/views/client/seo/landing.blade.php`, insert after the main `.seo-landing` section and before FAQ:

```blade
@if(in_array($landing['path'], ['/vkhidni-dveri-lutsk', '/mizhkimnatni-dveri-lutsk', '/dveri-volyn'], true))
    @include('client.shared.real-works-preview', [
        'works' => $realWorksPreview,
        'context' => ltrim($landing['path'], '/'),
    ])
@endif
```

- [ ] **Step 6: Add open preview-grid styles**

Append:

```scss
.real-works-preview {
  padding: 68px 0;
  background: #fff;

  &__heading {
    display: flex;
    align-items: end;
    justify-content: space-between;
    gap: 28px;
    margin-bottom: 28px;
  }

  &__grid {
    display: grid;
    grid-template-columns: repeat(4, minmax(0, 1fr));
    gap: 14px;

    a {
      color: #17191e;
      text-decoration: none;
    }

    img {
      display: block;
      width: 100%;
      aspect-ratio: 3 / 4;
      object-fit: cover;
    }

    span {
      display: block;
      margin-top: 10px;
      font-weight: 700;
      line-height: 1.4;
    }
  }
}

@media (max-width: 767px) {
  .real-works-preview {
    padding: 48px 0;

    &__heading {
      align-items: flex-start;
      flex-direction: column;
    }

    &__grid {
      grid-template-columns: repeat(2, minmax(0, 1fr));
    }
  }
}

@media (max-width: 360px) {
  .real-works-preview__grid {
    grid-template-columns: 1fr;
  }
}
```

- [ ] **Step 7: Run preview tests and production build**

Run:

```powershell
docker compose exec -T app php artisan test --filter RealWorksTest
npm run production
```

Expected: all `RealWorksTest` tests pass and build exits 0.

- [ ] **Step 8: Commit page integrations**

```powershell
git add -- app/Http/Controllers/MainController.php app/Http/Controllers/SeoController.php resources/views/client/main/index.blade.php resources/views/client/seo/landing.blade.php resources/views/client/shared/real-works-preview.blade.php resources/views/client/works/partials/case.blade.php resources/client/scss/_real-works.scss tests/Feature/RealWorksTest.php public/mix-manifest.json
git diff --cached --check
git commit -m "feat: surface real installations on local pages"
```

---

### Task 6: Complete sitemap, AI visibility, regression, and browser verification

**Files:**

- Modify: `app/Support/SeoContent.php`
- Modify: `app/Http/Controllers/SeoController.php`
- Modify: `resources/views/client/knowledge/for-ai-agents.blade.php`
- Modify: `tests/Feature/RealWorksTest.php`
- Modify: `tests/Feature/SeoKnowledgeTest.php`

**Interfaces:**

- Consumes: `RealWorks::page()['path']`.
- Produces sitemap location `https://metrnametr.com.ua/nashi-roboty`.
- Produces AI discovery reference to the same canonical URL.

- [ ] **Step 1: Add failing sitemap and AI tests**

Append:

```php
public function testWorksPageIsDiscoverableInSitemapAndAiSources()
{
    $sitemap = $this->get('/sitemap.xml')->assertStatus(200)->getContent();
    $this->assertStringContainsString('<loc>https://metrnametr.com.ua/nashi-roboty</loc>', $sitemap);
    $this->assertStringNotContainsString('localhost', $sitemap);
    $this->assertStringNotContainsString('%20', $sitemap);
    preg_match_all('/<loc>(.*?)<\/loc>/', $sitemap, $locations);
    foreach ($locations[1] as $location) {
        $this->assertStringNotContainsString(' ', $location);
    }

    foreach (['/for-ai-agents', '/llms.txt', '/llms-full.txt', '/ai-policy.txt'] as $url) {
        $content = $this->get($url)->assertStatus(200)->getContent();
        $this->assertStringContainsString('/nashi-roboty', $content);
        $this->assertStringContainsString('реальн', mb_strtolower($content));
    }
}
```

Also extend `testSeoLandingPagesAreIncludedInSitemapAndLlmsFiles()` in `SeoKnowledgeTest.php` with:

```php
->assertSee('<loc>https://metrnametr.com.ua/nashi-roboty</loc>', false)
```

- [ ] **Step 2: Run discovery tests and verify RED**

Run:

```powershell
docker compose exec -T app php artisan test --filter "WorksPageIsDiscoverable|SeoLandingPagesAreIncluded"
```

Expected: FAIL because the new URL is absent from sitemap and AI sources.

- [ ] **Step 3: Add the sitemap entry**

Add after the homepage item in `SeoContent::sitemapUrls()`:

```php
self::staticSitemapUrl(
    RealWorks::page()['path'],
    'config/real_works.php',
    'monthly',
    '0.8'
),
```

- [ ] **Step 4: Add AI-source references**

In `SeoController::llms()`, add under Important URLs:

```php
'- Реальні встановлення дверей: ' . $site['domain'] . RealWorks::page()['path'],
```

In `SeoController::llmsFull()`, add before the final instructions:

```php
$lines[] = '';
$lines[] = 'Real installation evidence:';
$lines[] = '- ' . $site['domain'] . RealWorks::page()['path'] . ' - реальні фото встановлених вхідних та міжкімнатних дверей у Луцьку й Волинській області без публікації точних адрес клієнтів.';
```

In `SeoController::aiPolicy()`, add to preferred sources and restrictions:

```php
'Preferred evidence for реальні приклади встановлених дверей: ' . $site['domain'] . RealWorks::page()['path'] . '.',
'- Do not infer exact client addresses, towns, product models or installation details from photos when the page does not state them.',
```

Ensure `SeoController` imports:

```php
use App\Support\RealWorks;
```

In `resources/views/client/knowledge/for-ai-agents.blade.php`, add one visible source row:

```blade
<li>
    <a href="{{ route('real-works.index') }}">Реальні встановлення дверей</a>
    — перевірені фото після монтажу у Луцьку та Волинській області; не виводьте точні адреси або деталі, яких немає на сторінці.
</li>
```

- [ ] **Step 5: Run the complete automated suite**

Run:

```powershell
docker compose exec -T app php artisan test
npm test
npm run production
```

Expected:

- PHPUnit exits 0 with no failed tests.
- Node test runner reports 3 passing tests and 0 failures.
- Laravel Mix production build exits 0.

- [ ] **Step 6: Verify routes and response metadata over HTTP**

Run:

```powershell
$urls = @(
  'http://localhost:8081/nashi-roboty',
  'http://localhost:8081/',
  'http://localhost:8081/vkhidni-dveri-lutsk',
  'http://localhost:8081/mizhkimnatni-dveri-lutsk',
  'http://localhost:8081/dveri-volyn',
  'http://localhost:8081/sitemap.xml',
  'http://localhost:8081/for-ai-agents',
  'http://localhost:8081/llms.txt',
  'http://localhost:8081/llms-full.txt',
  'http://localhost:8081/ai-policy.txt'
)
$urls | ForEach-Object {
  $response = Invoke-WebRequest -Uri $_ -UseBasicParsing
  [pscustomobject]@{ Url = $_; Status = $response.StatusCode; Bytes = $response.RawContentLength }
}
```

Expected: every URL returns 200.

- [ ] **Step 7: Run Browser/IAB functional and responsive QA**

Use the Browser plugin as required by the frontend workflow:

1. Open `http://localhost:8081/nashi-roboty`.
2. Verify current `logo-2.png` header and the exact H1.
3. Verify all five cases and their natural Ukrainian text.
4. Select every filter and verify visible cases match the declared category tags.
5. Open lightbox, use next/previous, Escape, backdrop close, and verify focus returns.
6. Confirm network log contains no request for `door-overview.mp4` before click.
7. Click the video poster and confirm the real door video plays.
8. Test `1440×900`, `390×844`, and `320×800`.
9. At 320 px, compare `document.documentElement.scrollWidth` with `clientWidth`; they must be equal.
10. Inspect the homepage and three landings for correct preview counts and uncut text.

Expected: no console error, no broken asset, no horizontal scroll, no inaccessible hidden control, and no unrelated video.

- [ ] **Step 8: Capture implementation screenshots and perform fidelity QA**

Capture:

- desktop `/nashi-roboty` at 1440×900;
- mobile `/nashi-roboty` at 390×844;
- homepage preview at desktop width.

Use `view_image` on the latest desktop implementation screenshot and the approved editorial-direction reference from the design specification. Record at least these comparison points:

1. current logo and existing header preserved;
2. open editorial layout rather than nested cards;
3. large real-photo treatment and alternating case rhythm;
4. restrained palette and yellow CTA treatment;
5. compact filters and visible active/focus state;
6. video band visually separated and below the cases;
7. mobile single-column layout without clipped copy.

Expected: no material mismatch remains. If the ignored brainstorming reference is unavailable, use the durable design specification and the approved logo requirement as the comparison source, and state that limitation explicitly.

- [ ] **Step 9: Validate schema locally**

Run:

```powershell
$html = (Invoke-WebRequest -Uri 'http://localhost:8081/nashi-roboty' -UseBasicParsing).Content
$scripts = [regex]::Matches($html, '<script type="application/ld\\+json">(.*?)</script>', 'Singleline')
$scripts | ForEach-Object { $_.Groups[1].Value | ConvertFrom-Json | Out-Null }
"Validated JSON-LD blocks: $($scripts.Count)"
```

Expected: every JSON-LD block parses and the count is at least 5 after default schemas, breadcrumb, and gallery schema are combined.

External Google Rich Results validation is a post-deployment check because the local page is not publicly crawlable.

- [ ] **Step 10: Review final diff and commit discovery updates**

Run:

```powershell
git status --short
git diff --check
git diff
git add -- app/Support/SeoContent.php app/Http/Controllers/SeoController.php resources/views/client/knowledge/for-ai-agents.blade.php tests/Feature/RealWorksTest.php tests/Feature/SeoKnowledgeTest.php public/mix-manifest.json
git diff --cached --check
git commit -m "feat: expose real installations to search and AI"
```

- [ ] **Step 11: Final Git verification and push**

Run:

```powershell
git status --short
git log --oneline -8
git branch --show-current
git remote -v
git push -u origin HEAD
```

Expected:

- working tree is clean;
- branch remains `codex/docker-port-8081`;
- push succeeds to `origin`;
- final response reports files changed, all validation commands, commit hashes, branch, push result, and the external Rich Results check deferred until deployment.
