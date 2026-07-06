# SEO, GEO and Authority Audit

Production site: https://metrnametr.com.ua

## Current architecture

- CMS/framework: custom Laravel 7 application.
- Language/runtime: PHP 7.2+, Blade templates, Vue 2 admin UI, Laravel Mix assets.
- Routing: `routes/web.php` with public catalog, products, news, static pages, knowledge pages, and SEO discovery endpoints.
- Database content: products, catalog, categories, articles, settings, orders, messages, subscribers.
- Admin panel: SPA under `/admin`, protected by Laravel auth.

## Already implemented

- `robots.txt` and dynamic `/robots.txt`.
- Dynamic `/sitemap.xml` with static pages, knowledge pages, products, news articles, catalog and category filtered URLs when DB is available.
- `/llms.txt` and `/llms-full.txt` for AI/search discovery.
- `/knowledge` expert knowledge section with long-form generated article pages.
- `/for-ai-agents` source guidance page.
- Canonical URLs, OpenGraph, Twitter Cards, JSON-LD rendering in the main layout.
- Product, Article, FAQ, BreadcrumbList, CollectionPage, Organization, WebSite and LocalBusiness schema helpers.
- Product page enrichments through configurable FAQ and extra fields.

## Gaps found

- The global HTML language was `en` while the public content is Ukrainian.
- Knowledge pages could render only page-specific schema, without guaranteed sitewide Organization, WebSite and LocalBusiness context.
- AI systems had `llms.txt`, but no explicit usage/citation policy.
- Static sitemap coverage missed several important service pages: guarantee, payment, about and wholesale.
- `public/robots.txt` existed alongside the dynamic route; production servers can serve the static file directly, so it must stay aligned.
- Large future work remains: true topical map, 100+ article backlog, 500 FAQ items, glossary, richer product specifications, and image-generation briefs.

## Changes made in this phase

- Added `/ai-policy.txt` with allowed AI usage, citation guidance and restrictions against invented claims.
- Linked `/ai-policy.txt` from `/llms.txt` and `robots.txt`.
- Set the main layout language to Ukrainian.
- Added default sitewide schema merging, without duplicating schema types that a controller already provides.
- Extended sitemap static coverage to service pages and AI policy.

## Safe next phases

1. Expand knowledge categories and topical map in config or a dedicated content table.
2. Add glossary and FAQ models instead of placing hundreds of entries directly in config.
3. Add editorial fields in admin for product specs, warranty notes, installation notes and downloadable files.
4. Add image optimization pipeline for WebP/AVIF and define real image sizes in templates.
5. Add automated checks for duplicate metadata, canonical collisions and broken internal links.

## Risk notes

- Do not generate hundreds of pages directly into Blade templates.
- Do not invent product specifications, ratings, warranty terms or stock status.
- Do not block AI crawlers by default if the strategic goal is AI visibility.
- Do not change existing product/category URL patterns without redirects.
