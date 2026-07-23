# SEO and Commercial Landing Pages Design

**Date:** 2026-07-23  
**Project:** metrnametr.com.ua  
**Status:** Approved for implementation planning

## Objective

Turn the local SEO pages into useful commercial landing pages that generate
door-selection and measurement enquiries in Lutsk and Volyn. The implementation
must preserve the current visual language, use only confirmed catalog and real
work data, and avoid invented prices, availability, specifications, guarantees,
or project details.

## Confirmed data policy

- Published `Product` records are the only source for model names, images,
  prices, product URLs, and product-specific characteristics.
- A numeric price is rendered only when the stored value is greater than zero.
- When no confirmed price is available, the card says:
  `Ціну уточнюйте після підбору комплектації`.
- Missing characteristics are omitted. They are not replaced with generic
  claims.
- Existing cases exposed by `App\Support\RealWorks` and `config/real_works.php`
  are the only source for real installation photos and case details.
- Product and work records must not be duplicated merely to reach a requested
  card count.
- When a matching landing has fewer than six suitable published products, the
  page renders the smaller truthful set and keeps a catalog CTA visible.

## Architecture

The existing configuration-driven SEO landing system remains the foundation.
`config/seo_landings.php` and `config/seo_landings_overrides.php` define the
page-specific copy, metadata, FAQ, internal links, and catalog selection intent.
`SeoController` resolves the landing configuration and supplies a filtered
collection of published products plus relevant `RealWorks` cases.

Reusable Blade partials render the repeated commercial sections. This avoids
six separate, nearly identical page templates and lets current and future local
landings use the same accessible, responsive components. Page-specific copy
continues to be configuration data rather than large conditional blocks in the
main landing template.

The implementation adds no frontend framework or runtime dependency. It uses
the project's existing Laravel, Blade, Bootstrap grid conventions, SCSS,
Eloquent models, PHPUnit suite, and Node test runner.

## Pages in scope

### Existing landing pages

- `/vkhidni-dveri-lutsk`
- `/mizhkimnatni-dveri-lutsk`
- `/dveri-volyn`
- `/dveri-z-montazhem-lutsk`

The first page receives the strongest commercial treatment. The other existing
pages receive the reusable commercial blocks where their content and product
selection are relevant. Existing useful copy and conversion sections are
preserved, while overlapping hard-coded sections are consolidated.

### New landing pages

- `/metalovi-dveri-lutsk`
- `/bronovani-dveri-lutsk`
- `/vkhidni-dveri-v-budynok-lutsk`
- `/vkhidni-dveri-v-kvartyru-lutsk`
- `/mizhkimnatni-dveri-z-montazhem-lutsk`

`/dveri-z-montazhem-lutsk` already exists and is extended rather than
re-created. Titles and H1 values for all requested pages follow the supplied
brief exactly unless the existing approved title is more specific without
changing search intent.

### Real work page

`/nashi-roboty` remains the canonical real-installations page. Its existing
filters, cases, photos, CTA controls, canonical URL, Open Graph image,
breadcrumbs, and gallery schema are retained. The implementation adds only
missing internal-link or schema coverage discovered by tests; it does not
invent new cases or locations.

### Knowledge base

Published knowledge articles are audited programmatically and editorially for:

- exactly one H1;
- logical H2/H3 order;
- non-duplicative FAQ content;
- natural Ukrainian copy without mechanical keyphrase repetition;
- descriptive title and meta description;
- a comparison, buyer-mistakes, or pre-order checklist block when relevant;
- links to the catalog and contextually relevant commercial landing pages;
- an existing generated or thematic image with descriptive alt text.

Articles are changed only when the audit demonstrates a concrete issue. No
technical facts, prices, warranty terms, or service promises are introduced
without a source already present in the project.

## Commercial page composition

Each applicable landing uses this reading order:

1. Breadcrumbs.
2. H1 and two or three short introductory paragraphs.
3. Popular models grid.
4. Context-specific selection and installation block.
5. Mid-page enquiry CTA.
6. Real installation preview.
7. Six-step ordering process.
8. Price-factor explanation.
9. Contextual internal links.
10. Five to seven FAQ items.
11. Final enquiry CTA.

The entrance-door page may retain its current commercial hero before the
introductory copy, because it is already part of the site's conversion design.
The hero must not introduce a second H1.

## Product selection and cards

The controller or a focused support class maps each landing intent to suitable
catalog categories and product types using existing relationships. Selection
is deterministic, contains unique product IDs, includes published products
only, and is capped at twelve items.

Each product card may render:

- confirmed product image and descriptive alt;
- product name;
- confirmed category or intended use derived from existing catalog data;
- only confirmed characteristics such as canvas thickness, insulation, locks,
  finish, or thermal break;
- confirmed numeric price, or the approved price clarification;
- confirmed availability only when such a field exists and has a meaningful
  value;
- `Запитати ціну`;
- `Замовити замір`;
- `Детальніше`.

`Детальніше` links to the canonical product page. Enquiry and measurement
actions use the existing contact/order workflow and approved analytics event
names. Cards remain vertically readable on mobile; actions wrap without causing
horizontal scrolling.

## Installation and ordering content

The installation block explains that the customer can request consultation,
selection, measurement, delivery in Lutsk and Volyn, and installation after the
configuration is agreed. It states that the final scope depends on the opening,
old-door removal, extensions, trim, threshold, and additional work.

The visible service list contains:

- consultation before ordering;
- opening-size verification;
- selection for an apartment or house;
- delivery in Lutsk and Volyn;
- installation after configuration approval.

The order process uses six steps:

1. Consultation.
2. Preliminary selection.
3. Measurement.
4. Configuration approval.
5. Delivery.
6. Installation.

The price-factor section covers door type, dimensions, canvas thickness, locks,
insulation, finish, extensions or trim, delivery, and installation. It remains
an explanatory list and does not imply unconfirmed price ranges.

## Enquiry workflow

The commercial blocks route users into the existing contact/order workflow.
The form requests name, telephone, door type, and comment. A photo-opening
action is displayed only if the existing backend supports receiving the photo;
otherwise the UI offers a truthful contact CTA for sending a photo through an
already published contact channel.

The primary CTA labels are:

- `Запитати ціну`;
- `Замовити замір`;
- `Надіслати фото отвору для попередньої консультації`.

Form validation and submission behavior remain consistent with existing site
forms. Success and error states must be perceivable without relying only on
color.

## Internal linking

Commercial pages link contextually to their closest neighbors rather than
showing the same link list everywhere. The network includes:

- entrance doors in Lutsk;
- doors with installation in Lutsk;
- metal doors in Lutsk;
- armored doors in Lutsk;
- entrance doors for a house;
- entrance doors for an apartment;
- interior doors with installation;
- doors in Volyn;
- catalog;
- real installations.

The home page, relevant local landings, and footer or useful-pages section link
to `/nashi-roboty`. Knowledge articles link to the catalog and one or more
relevant commercial pages where the link helps the reader take the next step.

## SEO and structured data

Every new page must:

- return HTTP 200;
- expose one H1;
- use its configured title and meta description;
- emit an absolute self-referencing canonical URL;
- use a relevant Open Graph image from confirmed project assets;
- appear once in `sitemap.xml`;
- remain crawlable under `robots.txt`;
- emit valid `BreadcrumbList`;
- emit `FAQPage` only when the page has a non-empty FAQ;
- emit the existing landing/service schema;
- emit `ImageObject` or gallery schema only for confirmed project images.

The sitemap must contain no localhost, encoded-space, test, or parameterized
URLs. New pages must also be discoverable through the project's AI discovery
files when those files enumerate SEO landing pages.

## Visual and accessibility requirements

- Preserve the current Lato typography, color palette, button language,
  Bootstrap breakpoints, and overall site identity.
- Reuse existing shared components and SCSS conventions.
- Do not introduce a new UI framework or dependency.
- Use semantic sections, headings, lists, articles, labels, and buttons.
- Maintain visible keyboard focus and meaningful link/button names.
- Give every meaningful image a descriptive Ukrainian alt attribute.
- Use available WebP variants with a supported fallback.
- Reserve image dimensions to reduce layout shift.
- Ensure the product grid, process steps, FAQ, and CTA controls fit a narrow
  mobile viewport without horizontal scrolling.
- Respect the current reduced-motion behavior and avoid unnecessary animation.

## Failure and empty states

- No matching products: omit the empty grid, show a short truthful explanation,
  and provide links to the full catalog and consultation.
- Missing product image: use the project's established product placeholder with
  truthful alt text, without representing it as the actual model.
- Missing price: show the approved clarification text.
- Missing optional specification: omit the row.
- Missing relevant work preview: omit that preview rather than using an
  unrelated case.
- Unavailable photo upload: do not render an upload input or imply that the
  website accepts files.

## Testing and verification

Implementation follows test-first development:

- feature tests fail first for new routes, exact metadata, H1 count, canonical,
  commercial blocks, internal links, sitemap, AI discovery, and schema;
- unit or feature tests verify deterministic unique product selection and
  truthful missing-data fallbacks;
- existing `RealWorksTest` continues to verify real assets and schema;
- knowledge tests cover heading, metadata, image, and internal-link rules;
- Node tests cover any new client-side interaction;
- the full PHPUnit and Node suites pass;
- the production asset build succeeds;
- desktop and mobile browser checks cover every new page template state,
  working CTA paths, image loading, and absence of horizontal overflow;
- rendered structured data is inspected for critical validation errors.

## Out of scope

- New CMS tables or admin editing interfaces.
- Invented stock statuses, technical specifications, reviews, ratings, prices,
  guarantees, delivery times, or installation prices.
- New real-work cases or claims about exact client locations.
- Radical redesign, rebranding, or a new frontend framework.
- A new file-upload backend unless separately approved.

