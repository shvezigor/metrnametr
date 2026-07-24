# Homepage SEO Links and Product Language Cleanup Design

**Date:** 2026-07-24
**Status:** Approved and implemented

## Goal

Make the five new Lutsk commercial landing pages directly reachable from the
home page and footer, while removing confirmed Ukrainian-language mistakes
from product copy used by visitors, search engines, and AI agents.

## Scope

### Homepage navigation

Add a dedicated section titled `Популярні рішення для дверей у Луцьку` between
the local commercial introduction and the general category grid. The section
will contain direct links to:

- `/metalovi-dveri-lutsk`;
- `/bronovani-dveri-lutsk`;
- `/vkhidni-dveri-v-budynok-lutsk`;
- `/vkhidni-dveri-v-kvartyru-lutsk`;
- `/mizhkimnatni-dveri-z-montazhem-lutsk`.

The section will be a semantic navigation landmark with an H2, one concise
introductory paragraph, and five compact link cards. Anchor text will be
natural Ukrainian and describe the destination without repeating exact
keyphrases in surrounding copy. The page will retain exactly one H1.

The cards will reuse the existing Lato typography, blue/yellow palette,
focus-state conventions, and responsive Bootstrap breakpoints. The grid will
render three columns on desktop, two on tablet, and one on narrow mobile
screens without horizontal overflow.

### Footer navigation

Expand the existing `Популярні запити` list with the five new destinations.
Keep the labels shorter than the home-page card titles and preserve the
existing useful local links. The footer remains a compact navigation aid, not
a second long SEO-content block.

### Product-language normalization

Introduce one focused support component that normalizes only reviewed,
unambiguous mistakes. It will operate on plain text and HTML without changing
tags, brand names, model codes, measurements, prices, or technical values.

The initial reviewed replacements include:

- `вхідін`, `вхідіні` → `вхідні`;
- `зовнішін`, `зовнішіні` → `зовнішні`;
- `с двух сторін` → `з двох сторін`;
- `В комплект входить` / `в комплект входить` → `До комплекту входить`;
- `Підїздні`, `підїздні` → `Під’їзні`, `під’їзні`;
- `2 контура` → `2 контури`;
- `на 1 сторону` → `з одного боку`;
- `додатковий завіс` → `додаткова завіса`.

Additional replacements may be added only when tests demonstrate a concrete,
unambiguous Ukrainian error. Stylistic preferences and potentially meaningful
catalog terminology will not be changed automatically.

## Architecture and Data Flow

1. A small `ProductCopy` support class receives a nullable string and returns
   normalized content using an explicit ordered replacement map.
2. Product model accessors normalize `title`, `text`, `description`,
   `seo_title`, and `seo_description` whenever the application renders or
   serializes a product.
3. Matching mutators normalize those fields before future product saves.
4. A data migration reads the raw stored values, applies the same support
   component, and updates only rows whose content changes.
5. Product pages, catalog cards, commercial landing cards, metadata, and
   AI/search outputs therefore consume the same corrected source.

The migration will have a no-op rollback because restoring known linguistic
errors would be harmful and could overwrite later editorial changes.

## SEO and Accessibility Rules

- Keep exactly one H1 on the home page.
- Use a descriptive H2 and semantic `<nav aria-labelledby="…">`.
- Use crawlable server-rendered `<a href>` links without JavaScript routing.
- Use distinct, human-readable Ukrainian anchor text.
- Do not add hidden copy, keyword lists, duplicate paragraphs, or invented
  commercial claims.
- Preserve visible keyboard focus and responsive behavior.
- Keep existing canonical, Open Graph, schema, sitemap, and `llms` behavior.

## Testing

Follow a red-green workflow:

1. Feature test: the home page and footer expose all five direct URLs, use the
   expected navigation landmark, and retain one H1.
2. Unit test: the normalizer fixes every reviewed phrase while preserving HTML,
   brands, model codes, sizes, and already-correct Ukrainian text.
3. Model test: new values are normalized when saved and read.
4. Feature test: a product containing dirty source text renders corrected copy
   and does not expose the reviewed Russian or misspelled variants.
5. Migration test or database assertion: existing matching rows are updated
   without altering unrelated products.
6. Run the full PHPUnit suite, Node tests, production asset build, and local
   HTTP smoke checks.

## Files Expected to Change

- `resources/views/client/main/index.blade.php`
- `resources/views/client/shared/footer.blade.php`
- `resources/client/scss/_common.scss`
- `app/Support/ProductCopy.php`
- `app/Models/Product.php`
- `database/migrations/*_normalize_ukrainian_product_copy.php`
- product/homepage feature and unit tests
- tracked Laravel Mix manifest when its generated hash changes

No new frontend framework, runtime dependency, CMS field, or external service
is required.
