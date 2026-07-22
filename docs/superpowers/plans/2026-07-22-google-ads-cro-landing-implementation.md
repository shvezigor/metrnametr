# Google Ads CRO Landing Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Додати комерційний перший екран, CRO-блоки та вимірювання конверсій для локальних рекламних посадкових сторінок без зміни чинної дизайн-системи.

**Architecture:** Окремий Blade partial відображає рекламний hero на головній та сторінці вхідних дверей. Контент комплектацій і монтажу живе у цільовому Blade-шаблоні, FAQ — у наявній SEO-конфігурації, а делегований JavaScript-відстежувач передає стандартизовані GA4-події.

**Tech Stack:** Laravel 7, Blade, SCSS, jQuery, GA4 gtag, PHPUnit.

## Global Constraints

- Не додавати frontend-залежностей і не змінювати радикально наявний дизайн.
- Не видаляти LocalBusiness schema, llms.txt, for-ai-agents, ai-policy.txt, Google Maps CID або мобільний CTA.
- Не публікувати вигаданих цін; використовувати «Точна ціна після заміру».
- Зберегти GA4 `G-Y8R2Q5QV0F` і не повертати `UA-25922813-1`.

---

### Task 1: Контентні та SEO acceptance-тести

**Files:**
- Modify: `tests/Feature/SeoKnowledgeTest.php`

**Interfaces:**
- Consumes: Laravel HTTP test client.
- Produces: регресійні твердження для hero, комплектацій, FAQ, CTA, аналітики та незмінних AI/SEO endpoint.

- [ ] Додати тест, який перевіряє 200, відсутність `noindex`, точний hero, «Популярні комплектації вхідних дверей», п’ять карток, «Точна ціна після заміру» і три CTA.
- [ ] Додати тест головної сторінки з офером «Двері у Луцьку та Волині з заміром і монтажем» і трьома CTA.
- [ ] Додати тест GA4, відсутності UA, data-атрибутів для п’яти подій та збереження AI/SEO endpoint.
- [ ] Запустити `vendor/bin/phpunit --filter=GoogleAdsCro` і підтвердити очікуване падіння через відсутній новий UI.

### Task 2: Hero, комплектації, монтаж і FAQ

**Files:**
- Create: `resources/views/client/shared/commercial-hero.blade.php`
- Modify: `resources/views/client/main/index.blade.php`
- Modify: `resources/views/client/seo/landing.blade.php`
- Modify: `config/seo_landings_overrides.php`
- Modify: `resources/client/scss/_common.scss`

**Interfaces:**
- Consumes: `title`, `text`, `image`, `askPriceLabel` і наявні routes/settings.
- Produces: доступний responsive hero та комерційні секції лише для `/vkhidni-dveri-lutsk`.

- [ ] Реалізувати повторно використовуваний hero з трьома CTA і чотирма trust signals.
- [ ] Замінити лише перший екран головної на уточнений офер, не змінюючи решту сторінки.
- [ ] Додати hero, п’ять комплектів, шість пунктів заміру/монтажу та повторний CTA на цільову сторінку.
- [ ] Оновити п’ять FAQ короткими комерційними відповідями у конфігурації, щоб JSON-LD використовував той самий текст.
- [ ] Додати адаптивні стилі з видимим focus state, мінімальною областю натискання та mobile safe-area.
- [ ] Запустити `vendor/bin/phpunit --filter=GoogleAdsCro` і підтвердити green.

### Task 3: GA4 conversion events і Ads mapping

**Files:**
- Modify: `resources/client/js/app.js`
- Modify: `resources/views/client/shared/mobile-contact-cta.blade.php`
- Modify: `resources/views/client/shared/google-map-embed.blade.php`
- Modify: `resources/views/client/shared/status.blade.php`
- Create: `docs/google-ads-search-plan.md`

**Interfaces:**
- Consumes: `data-ga-event`, `data-cta-location`, `window.gtag`, Laravel success flash.
- Produces: `phone_click`, `ask_price_click`, `catalog_click`, `maps_route_click`, `generate_lead`.

- [ ] Додати делегований безпечний GA4 handler для CTA, який не перешкоджає переходам.
- [ ] Позначити мобільні CTA, Google Maps і підтвердження форми відповідними data-атрибутами.
- [ ] Створити Google Ads документ із final URL mapping, групами exact/phrase, мінус-словами, RSA assets, extensions, 30-денним A/B-планом і щотижневим чеклістом.
- [ ] Запустити контентні тести та production frontend build.

### Task 4: Повна перевірка і Git

**Files:**
- Verify all modified files.

**Interfaces:**
- Consumes: локальний HTTP server і production assets.
- Produces: перевірений diff, commit і push у поточну task branch.

- [ ] Запустити повний PHPUnit suite та `npm run production`.
- [ ] Перевірити 200/noindex/GA4/UA/hero/CTA/комплектації автоматичними HTTP-запитами.
- [ ] Перевірити desktop і mobile render, видимість CTA та одну взаємодію у Browser plugin; якщо Browser недоступний — Playwright fallback із зафіксованою причиною.
- [ ] Переглянути `git diff --check`, `git diff` і `git status --short`.
- [ ] Закомітити лише релевантні файли та виконати `git push -u origin HEAD`.
