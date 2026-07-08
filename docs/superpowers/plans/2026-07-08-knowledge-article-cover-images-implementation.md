# Knowledge Article Cover Images Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Add topic-aware image metadata and raster-ready cover image support for every `/knowledge/...` article.

**Architecture:** Add a focused `App\Support\KnowledgeImage` helper that classifies articles and returns cover metadata. Wire that helper into `KnowledgeArticleFactory`, `SeoContent`, and the knowledge Blade views while keeping the existing SVG route as fallback.

**Tech Stack:** Laravel 7, PHP 7.2+, Blade, PHPUnit.

## Global Constraints

- Scope is `/knowledge/...` articles only.
- Do not edit `/news` database articles.
- Do not add UI frameworks or image generation dependencies.
- Use `/images/knowledge/<slug>.webp` as the preferred raster URL.
- Keep `/knowledge/{slug}/image.svg` as fallback when the raster file is missing.
- Prompts must request realistic commercial photography for the Ukrainian door market.
- Prompts must require horizontal 16:9, no text, no logos, and no watermarks.

---

### Task 1: Test Knowledge Image Metadata

**Files:**
- Modify: `tests/Feature/KnowledgePlanTest.php`

**Interfaces:**
- Consumes: `SeoContent::articles()`.
- Produces: failing assertions that require `image` metadata on every knowledge article.

- [ ] **Step 1: Write the failing test**

Add a test method that iterates through `SeoContent::articles()` and asserts each article contains `image.filename`, `image.src`, `image.fallback`, `image.alt`, `image.title`, `image.caption`, and `image.prompt`.

- [ ] **Step 2: Run test to verify it fails**

Run: `vendor/bin/phpunit --filter KnowledgePlanTest`

Expected: FAIL because articles do not yet have an `image` key.

### Task 2: Implement KnowledgeImage Helper

**Files:**
- Create: `app/Support/KnowledgeImage.php`
- Modify: `app/Support/KnowledgeArticleFactory.php`

**Interfaces:**
- Produces: `KnowledgeImage::forArticle(array $article): array`.
- Produces article field: `$article['image']`.

- [ ] **Step 1: Implement helper**

Create `KnowledgeImage` with:

- `forArticle(array $article): array`;
- `classify(array $article): array`;
- `rasterExists(string $filename): bool`;
- bucket definitions for entrance, house, interior, commercial, fire/security, locks, installation, production, insulation, coating/design/care, and general selection.

- [ ] **Step 2: Attach metadata**

In `KnowledgeArticleFactory::make()`, call `KnowledgeImage::forArticle($article)` after building the base article array and return it with an `image` key.

- [ ] **Step 3: Run metadata test**

Run: `vendor/bin/phpunit --filter KnowledgePlanTest`

Expected: PASS.

### Task 3: Wire Rendering And Schema

**Files:**
- Modify: `app/Support/SeoContent.php`
- Modify: `resources/views/client/knowledge/index.blade.php`
- Modify: `resources/views/client/knowledge/show.blade.php`
- Modify: `tests/Feature/KnowledgePlanTest.php`

**Interfaces:**
- Consumes: `$article['image']['src']`, `alt`, `title`, `caption`.
- Produces: `SeoContent::articleImageUrl(array $article)` returns the visible image URL.

- [ ] **Step 1: Update tests**

Change image assertions from direct `/knowledge/<slug>/image.svg` expectations to the fallback-aware `image.src` behavior and add schema URL coverage.

- [ ] **Step 2: Verify tests fail**

Run: `vendor/bin/phpunit --filter KnowledgePlanTest`

Expected: FAIL until views and schema use the new metadata.

- [ ] **Step 3: Update code**

Use `$article['image']` in knowledge index/show image tags and captions. Change `SeoContent::articleImageUrl()` to return `canonical($article['image']['src'])` when present.

- [ ] **Step 4: Run focused tests**

Run: `vendor/bin/phpunit --filter KnowledgePlanTest`

Expected: PASS.

### Task 4: Full Validation And Git Finalization

**Files:**
- Review all changed files.

**Interfaces:**
- Produces: committed and pushed implementation.

- [ ] **Step 1: Run validation**

Run: `vendor/bin/phpunit`

Expected: PASS.

- [ ] **Step 2: Review diff**

Run: `git status --short` and `git diff`.

- [ ] **Step 3: Commit**

Run: `git add <relevant files>` and `git commit -m "feat: add knowledge article image metadata"`.

- [ ] **Step 4: Push**

Run: `git push -u origin HEAD`.
