<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Category;
use App\Models\Type;
use App\Support\SeoContent;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class SeoKnowledgeTest extends TestCase
{
    use DatabaseTransactions;

    public function testLlmsTxtDescribesSiteAndReferencesKeyResources()
    {
        $response = $this->get('/llms.txt');

        $response->assertStatus(200);
        $response->assertSee('Метр на Метр');
        $response->assertSee('https://metrnametr.com.ua');
        $response->assertSee('/catalog');
        $response->assertSee('/knowledge');
        $response->assertSee('/sitemap.xml');
        $response->assertSee('/ai-policy.txt');
    }

    public function testRobotsAllowsCrawlersAndPointsToSitemap()
    {
        $response = $this->get('/robots.txt');

        $response->assertStatus(200);
        $response->assertSee('User-agent: *', false);
        $response->assertSee('Allow: /', false);
        $response->assertSee('Sitemap: https://metrnametr.com.ua/sitemap.xml', false);
        $response->assertDontSee('GPTBot');
        $response->assertDontSee('ClaudeBot');
    }

    public function testKnowledgeIndexAndArticleRenderStructuredContent()
    {
        $this->get('/knowledge')
            ->assertStatus(200)
            ->assertSee('База знань')
            ->assertSee('/knowledge/yak-vybraty-vkhidni-dveri-dlia-kvartyry');

        $this->get('/knowledge/yak-vybraty-vkhidni-dveri-dlia-kvartyry')
            ->assertStatus(200)
            ->assertSee('Як вибрати вхідні двері для квартири')
            ->assertSee('FAQPage')
            ->assertSee('Article')
            ->assertSee('id="comparison"', false);
    }

    public function testAiAgentPageAndSitemapExposeSeoResources()
    {
        $this->get('/for-ai-agents')
            ->assertStatus(200)
            ->assertSee('Сторінка для AI-агентів')
            ->assertSee('/llms.txt')
            ->assertSee('/knowledge');

        $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'application/xml')
            ->assertSee('<loc>https://metrnametr.com.ua/knowledge</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/guarantee</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/payment</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/about</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/wholesale</loc>', false)
            ->assertSee('<changefreq>weekly</changefreq>', false);
    }

    public function testAiPolicyExplainsAllowedUseAndCitationRules()
    {
        $response = $this->get('/ai-policy.txt');

        $response->assertStatus(200);
        $response->assertHeader('Content-Type', 'text/plain; charset=UTF-8');
        $response->assertSee('AI Usage Policy', false);
        $response->assertSee('Allowed: indexing, summarization, citation, answer generation', false);
        $response->assertSee('Do not invent ratings, reviews, warranty terms, stock status or technical specifications', false);
    }

    public function testKnowledgePagesExposeUkrainianLanguageAndSitewideSchema()
    {
        $this->get('/knowledge')
            ->assertStatus(200)
            ->assertSee('<html lang="uk">', false)
            ->assertSee('"@type": "Organization"', false)
            ->assertSee('"@type": "WebSite"', false)
            ->assertSee('"@type": "LocalBusiness"', false);
    }

    public function testHomepageRendersModernCommercialStructure()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('home-hero-modern')
            ->assertSee('home-category-grid')
            ->assertSee('home-trust-grid')
            ->assertSee('product-card-badges')
            ->assertSee('Вхідні та міжкімнатні двері від виробника у Луцьку')
            ->assertSee('Отримати консультацію')
            ->assertSee('Чому обирають Метр на Метр');
    }

    public function testHomepageRendersAboutStructureAndCompleteFaq()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('home-about-intro')
            ->assertSee('home-about-points')
            ->assertSee('Виробництво')
            ->assertSee('Якість')
            ->assertSee('Доставка та співпраця')
            ->assertSee('Які двері краще обрати для квартири?')
            ->assertSee('Чим відрізняються вуличні двері від квартирних?')
            ->assertSee('Як отримати консультацію перед покупкою?')
            ->assertSee('FAQPage');
    }

    public function testLayoutRendersMobileContactCta()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('mobile-contact-cta')
            ->assertSee('Подзвонити')
            ->assertSee('Запитати ціну')
            ->assertSee('Перейти в каталог')
            ->assertSee('href="' . route('contacts') . '"', false)
            ->assertSee('href="tel:', false);
    }

    public function testServicePagesRenderConsistentCtaBlock()
    {
        foreach ([route('guarantee'), route('payment'), route('wholesale'), route('contacts')] as $url) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('secondary-page-cta')
                ->assertSee('Перейти в каталог')
                ->assertSee('Отримати консультацію')
                ->assertSee('Подзвонити')
                ->assertSee('href="tel:', false);
        }
    }

    public function testProductPageRendersStructuredDecisionBlocks()
    {
        $product = factory(Product::class)->create([
            'title' => 'Door Comfort',
            'alias' => 'door-comfort',
            'published' => 1,
            'text' => '<p>Short product text.</p>',
            'extra_fields' => json_encode([
                'audience' => 'For apartments and private houses.',
                'benefits' => ['Reliable construction', 'Factory quality control'],
                'specs' => [
                    'Тип дверей' => 'Вхідні',
                    'Призначення' => 'Для квартири',
                    'Гарантія' => 'Від виробника',
                ],
            ]),
            'faq' => json_encode([
                ['question' => 'Can I request a consultation?', 'answer' => 'Yes, leave a request.'],
            ]),
        ]);

        $this->get(route('product.show', ['alias' => $product->alias]))
            ->assertStatus(200)
            ->assertSee('product-decision-grid')
            ->assertSee('product-spec-table')
            ->assertSee('Кому підходить')
            ->assertSee('Переваги моделі')
            ->assertSee('Технічні характеристики');
    }

    public function testProductBreadcrumbRendersBeforeProductColumns()
    {
        $product = factory(Product::class)->create([
            'title' => 'Door Layout',
            'alias' => 'door-layout',
            'published' => 1,
            'text' => '<p>Short product text.</p>',
        ]);

        $content = $this->get(route('product.show', ['alias' => $product->alias]))
            ->assertStatus(200)
            ->getContent();

        $this->assertLessThan(
            strpos($content, 'dot-slider'),
            strpos($content, 'breadcrumb-wrap')
        );
    }

    public function testCatalogPageRendersBuyerIntentGuide()
    {
        $this->get(route('catalog'))
            ->assertStatus(200)
            ->assertSee('catalog-intent-guide')
            ->assertSee('Каталог дверей у Луцьку')
            ->assertSee('Двері в квартиру')
            ->assertSee('Для гуртових клієнтів');
    }

    public function testLocalSeoHomeAndCatalogUseLutskVolynCommercialPhrases()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('<title>Двері у Луцьку та Волині — вхідні й міжкімнатні | Метр на Метр</title>', false)
            ->assertSee('Купити вхідні та міжкімнатні двері у Луцьку й Волині.', false)
            ->assertSee('<h1>Вхідні та міжкімнатні двері від виробника у Луцьку</h1>', false)
            ->assertSee('/vkhidni-dveri-lutsk', false)
            ->assertSee('/mizhkimnatni-dveri-lutsk', false)
            ->assertSee('/dveri-volyn', false)
            ->assertSee('/dveri-rivne', false)
            ->assertSee('/vkhidni-dveri-rivne', false)
            ->assertSee('/mizhkimnatni-dveri-rivne', false);

        $this->get(route('catalog'))
            ->assertStatus(200)
            ->assertSee('<title>Каталог дверей у Луцьку — вхідні, металеві та міжкімнатні | Метр на Метр</title>', false)
            ->assertSee('Каталог дверей Метр на Метр: вхідні, металеві та міжкімнатні двері у Луцьку.', false)
            ->assertSee('<h1>Каталог дверей у Луцьку</h1>', false)
            ->assertSee('У каталозі Метр на Метр можна підібрати вхідні, металеві та міжкімнатні двері', false)
            ->assertSee('/dveri-volyn', false)
            ->assertSee('/dveri-rivne', false);
    }

    public function testSeoSchemasUseAbsoluteImagesAndSkipEmptyFaqSchema()
    {
        config(['app.url' => 'https://metrnametr.com.ua']);

        $product = new Product([
            'title' => 'Test Door',
            'description' => 'Door description',
            'alias' => 'test-door',
            'price' => 0,
        ]);
        $product->setRelation('images', collect());

        $schema = SeoContent::productSchema($product);

        $this->assertSame('https://metrnametr.com.ua/images/placeholder-product.png', $schema['image']);
        $this->assertArrayNotHasKey('availability', $schema['offers'] ?? []);
        $this->assertNull(SeoContent::faqSchema([]));
        $this->assertSame('https://metrnametr.com.ua/images/og.jpg', SeoContent::ogImageFor((object) ['og_image' => '/images/og.jpg'], '/fallback.jpg'));
        $this->assertNotContains(null, SeoContent::defaultPageSchemas([SeoContent::faqSchema([])]));
    }

    public function testProductSchemaIncludesOnlyConfirmedSpecifications()
    {
        $product = new Product([
            'title' => 'Test Door',
            'alias' => 'test-door',
            'price' => 10000,
            'extra_fields' => json_encode([
                'brand' => 'Test Brand',
                'sku' => 'TD-100',
                'availability' => 'InStock',
                'width' => '900 mm',
                'height' => '2050 mm',
            ]),
        ]);
        $product->setRelation('images', collect());

        $schema = SeoContent::productSchema($product);

        $this->assertSame('TD-100', $schema['sku']);
        $this->assertSame('Test Brand', $schema['brand']['name']);
        $this->assertSame('900 mm × 2050 mm', $schema['size']);
        $this->assertSame('https://schema.org/InStock', $schema['offers']['availability']);
    }

    public function testSeoLandingPagesRenderMetaCanonicalFaqAndSchemas()
    {
        $pages = [
            '/vkhidni-dveri-lutsk' => 'Вхідні двері у Луцьку',
            '/mizhkimnatni-dveri-lutsk' => 'Міжкімнатні двері у Луцьку',
            '/dveri-dlya-kvartyry' => 'Двері для квартири',
            '/dveri-dlya-budynku' => 'Двері для будинку',
            '/dveri-z-termorozryvom' => 'Двері з терморозривом',
            '/protypozhezhni-dveri' => 'Протипожежні двері',
            '/dveri-volyn' => 'Двері у Волинській області',
            '/dveri-rivne' => 'Двері у Рівному',
            '/vkhidni-dveri-rivne' => 'Вхідні двері у Рівному',
            '/mizhkimnatni-dveri-rivne' => 'Міжкімнатні двері у Рівному',
        ];

        foreach ($pages as $url => $h1) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('<h1>' . $h1 . '</h1>', false)
                ->assertSee('<link rel="canonical" href="https://metrnametr.com.ua' . $url . '"', false)
                ->assertSee('FAQPage')
                ->assertSee('BreadcrumbList')
                ->assertSee('WebPage')
                ->assertSee('LocalBusiness')
                ->assertSee('проспект Перемоги, 24')
                ->assertSee('Популярні питання');
        }
    }

    public function testDoorInstallationLutskLandingRendersUkrainianSeoContentAndServiceSchema()
    {
        $this->get('/dveri-z-montazhem-lutsk')
            ->assertStatus(200)
            ->assertSee('<h1>Двері з монтажем у Луцьку</h1>', false)
            ->assertSee('<title>Двері з монтажем Луцьк — замір, доставка і встановлення | Метр на Метр</title>', false)
            ->assertSee('<link rel="canonical" href="https://metrnametr.com.ua/dveri-z-montazhem-lutsk"', false)
            ->assertSee('встановлення дверей Луцьк')
            ->assertSee('монтаж вхідних дверей Луцьк')
            ->assertSee('монтаж міжкімнатних дверей Луцьк')
            ->assertSee('"@type": "Service"', false)
            ->assertSee('"@type": "FAQPage"', false)
            ->assertSee('"@type": "BreadcrumbList"', false)
            ->assertSee('+380673343368');
    }

    public function testUpdatedLocalSeoLandingsExposeRequestedUkrainianCommercialBlocks()
    {
        $this->get('/mizhkimnatni-dveri-lutsk')
            ->assertStatus(200)
            ->assertSee('Міжкімнатні двері Луцьк — купити з коробкою, лиштвою та монтажем | Метр на Метр')
            ->assertSee('двері міжкімнатні з коробкою і лиштвою ціна Луцьк')
            ->assertSee('Двері зі склом')
            ->assertSee('Монтаж міжкімнатних дверей у Луцьку');

        $this->get('/vkhidni-dveri-lutsk')
            ->assertStatus(200)
            ->assertSee('Вхідні двері в квартиру у Луцьку')
            ->assertSee('Вхідні двері ціна')
            ->assertSee('Замір і монтаж');

        foreach (['/dveri-rivne', '/vkhidni-dveri-rivne', '/mizhkimnatni-dveri-rivne'] as $url) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('доставка дверей у Рівне')
                ->assertSee('працюємо з клієнтами з Рівного')
                ->assertDontSee('магазин у Рівному');
        }
    }

    public function testVolynSeoNextStepsExposeCityBlocksHomeLinksAndInstallationFaq()
    {
        $this->get('/dveri-volyn')
            ->assertStatus(200)
            ->assertSee('Двері Луцьк')
            ->assertSee('Двері Ковель')
            ->assertSee('Двері Нововолинськ')
            ->assertSee('Двері Володимир')
            ->assertSee('Двері Рожище')
            ->assertSee('Двері Ківерці');

        $this->get('/')
            ->assertStatus(200)
            ->assertSee('href="/dveri-volyn"', false)
            ->assertSee('href="/vkhidni-dveri-lutsk"', false)
            ->assertSee('href="/mizhkimnatni-dveri-lutsk"', false)
            ->assertSee('href="/dveri-z-montazhem-lutsk"', false);

        $this->get('/dveri-z-montazhem-lutsk')
            ->assertStatus(200)
            ->assertSee('Скільки коштує монтаж дверей у Луцьку?')
            ->assertSee('Що входить у замір дверей?')
            ->assertSee('Які терміни монтажу дверей?')
            ->assertSee('Чи можна замовити демонтаж старих дверей?');
    }

    public function testAdditionalLocalSeoAndAiVisibilityContentIsExposed()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Двері у Луцьку та Волині з монтажем')
            ->assertSee('вхідні двері Луцьк')
            ->assertSee('міжкімнатні двері Луцьк')
            ->assertSee('двері з монтажем у Луцьку')
            ->assertSee('двері у Волинській області')
            ->assertSee('Чому Метр на Метр рекомендують у Луцьку')
            ->assertSee('href="/dveri-z-montazhem-lutsk"', false);

        $this->get('/vkhidni-dveri-lutsk')
            ->assertStatus(200)
            ->assertSee('Двері з монтажем')
            ->assertSee('Як проходить замовлення')
            ->assertSee('Які вхідні двері краще обрати для квартири у Луцьку?')
            ->assertSee('Чи можна замовити вхідні двері з монтажем?')
            ->assertSee('Чи робите замір перед встановленням?')
            ->assertSee('Скільки часу займає доставка та монтаж?')
            ->assertSee('Чим відрізняються двері для квартири й приватного будинку?')
            ->assertSee('"@type": "Service"', false);

        $this->get('/dveri-z-montazhem-lutsk')
            ->assertStatus(200)
            ->assertSee('Що входить у монтаж дверей')
            ->assertSee('Замір перед встановленням')
            ->assertSee('Доставка по Луцьку та Волині')
            ->assertSee('Монтаж вхідних дверей')
            ->assertSee('Монтаж міжкімнатних дверей')
            ->assertSee('Демонтаж старих дверей')
            ->assertSee('Коли варто замовляти двері одразу з монтажем')
            ->assertSee('Отримати консультацію щодо монтажу')
            ->assertSee('Підібрати двері з установкою')
            ->assertSee('Зателефонувати: 067 334 33 68');

        $this->get('/dveri-volyn')
            ->assertStatus(200)
            ->assertSee('Працюємо по Волинській області')
            ->assertSee('Горохів')
            ->assertSee('Маневичі')
            ->assertSee('Любомль')
            ->assertSee('Камінь-Каширський')
            ->assertSee('href="/"', false)
            ->assertSee('href="/vkhidni-dveri-lutsk"', false)
            ->assertSee('href="/mizhkimnatni-dveri-lutsk"', false)
            ->assertSee('href="/dveri-z-montazhem-lutsk"', false);

        $this->get('/')
            ->assertSee('Популярні запити')
            ->assertSee('Вхідні двері Луцьк')
            ->assertSee('Міжкімнатні двері Луцьк')
            ->assertSee('Двері з монтажем Луцьк')
            ->assertSee('Двері Волинь');

        $this->get('/for-ai-agents')
            ->assertStatus(200)
            ->assertSee('виробник і магазин дверей у Луцьку')
            ->assertSee('/vkhidni-dveri-lutsk')
            ->assertSee('/mizhkimnatni-dveri-lutsk')
            ->assertSee('/dveri-z-montazhem-lutsk')
            ->assertSee('/dveri-volyn');

        $this->get('/llms.txt')
            ->assertStatus(200)
            ->assertSee('виробник і магазин дверей у Луцьку')
            ->assertSee('https://metrnametr.com.ua/vkhidni-dveri-lutsk')
            ->assertSee('https://metrnametr.com.ua/mizhkimnatni-dveri-lutsk')
            ->assertSee('https://metrnametr.com.ua/dveri-z-montazhem-lutsk')
            ->assertSee('https://metrnametr.com.ua/dveri-volyn');

        $schema = SeoContent::localBusinessSchema();
        $this->assertSame('43000', $schema['address']['postalCode']);
        $this->assertSame('Волинська область', $schema['address']['addressRegion']);
        $this->assertContains('Ковель', $schema['areaServed']);
        $this->assertContains('Володимир', $schema['areaServed']);
        $this->assertSame('https://www.google.com/maps?cid=15751063054979951698', $schema['hasMap']);
        $this->assertSame('GeoCoordinates', $schema['geo']['@type']);
        $this->assertContains($schema['hasMap'], $schema['sameAs']);
        $this->assertNotEmpty($schema['sameAs']);
    }

    public function testContactMapUsesGoogleMapsEmbedWithoutJavascriptApiKey()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('https://www.google.com/maps?q=', false)
            ->assertSee('output=embed', false)
            ->assertSee('Відкрити в Google Maps')
            ->assertDontSee('maps.googleapis.com/maps/api/js', false);

        $this->get(route('contacts'))
            ->assertStatus(200)
            ->assertSee('https://www.google.com/maps?q=', false)
            ->assertSee('output=embed', false)
            ->assertDontSee('maps.googleapis.com/maps/api/js', false);

        $this->get('/for-ai-agents')
            ->assertStatus(200)
            ->assertSee('Google Maps / Google Business Profile', false);

        $this->get('/llms.txt')
            ->assertStatus(200)
            ->assertSee('Google Maps profile', false);
    }

    public function testContactMapHasStableIframeDirectionsCtaAndNoJavascriptMapCode()
    {
        foreach (['/', route('contacts')] as $url) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('Метр на Метр на Google Maps')
                ->assertSee('https://www.google.com/maps?q=Метр%20на%20Метр,%20проспект%20Перемоги%2024,%20Луцьк,%20Волинська%20область,%2043000&output=embed', false)
                ->assertSee('loading="lazy"', false)
                ->assertSee('referrerpolicy="no-referrer-when-downgrade"', false)
                ->assertSee('Як нас знайти')
                ->assertSee('Метр на Метр на карті Луцька')
                ->assertSee('двері Луцьк')
                ->assertSee('вхідні двері Луцьк')
                ->assertSee('міжкімнатні двері Луцьк')
                ->assertSee('двері з монтажем Луцьк')
                ->assertSee('Волинська область')
                ->assertSee('https://www.google.com/maps/dir/?api=1&destination=проспект%20Перемоги%2024%20Луцьк', false)
                ->assertSee('Прокласти маршрут')
                ->assertSee('https://www.google.com/maps/search/?api=1&query=Метр%20на%20Метр%20проспект%20Перемоги%2024%20Луцьк', false)
                ->assertSee('Відкрити в Google Maps')
                ->assertSee('tel:+380673343368', false)
                ->assertSee('Зателефонувати')
                ->assertDontSee('maps.googleapis.com/maps/api/js', false)
                ->assertDontSee('map-canvas', false);
        }

        $this->assertStringNotContainsString('google.maps', file_get_contents(resource_path('client/js/app.js')));
        $this->assertStringNotContainsString('maps.googleapis.com/maps/api/js', file_get_contents(resource_path('client/js/app.js')));

        $schema = SeoContent::localBusinessSchema();
        $this->assertSame('LocalBusiness', $schema['@type']);
        $this->assertSame('Метр на Метр', $schema['name']);
        $this->assertSame('https://metrnametr.com.ua/', $schema['url']);
        $this->assertSame('+380673343368', $schema['telephone']);
        $this->assertSame('проспект Перемоги, 24', $schema['address']['streetAddress']);
        $this->assertSame('Луцьк', $schema['address']['addressLocality']);
        $this->assertSame('Волинська область', $schema['address']['addressRegion']);
        $this->assertSame('43000', $schema['address']['postalCode']);
        $this->assertSame('UA', $schema['address']['addressCountry']);
    }

    public function testSitemapIncludesNewSeoPageAndExcludesLowQualityOrParametricUrls()
    {
        $content = $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->getContent();

        $this->assertStringContainsString('<loc>https://metrnametr.com.ua/dveri-z-montazhem-lutsk</loc>', $content);
        $this->assertStringContainsString('<loc>https://metrnametr.com.ua/vkhidni-dveri-rivne</loc>', $content);
        $this->assertStringNotContainsString('localhost', $content);
        $this->assertStringNotContainsString('%20', $content);
        $this->assertStringNotContainsString('...', $content);
        $this->assertStringNotContainsString('catalog?categories', $content);
        $this->assertStringNotContainsString('catalog?catalog=', $content);
    }

    public function testSitemapUsesCanonicalUrlsWithHistoricalLastModifiedDates()
    {
        $today = now()->toDateString();
        $urls = collect(SeoContent::sitemapUrls());
        $staticLocations = [
            'https://metrnametr.com.ua/',
            'https://metrnametr.com.ua/catalog',
            'https://metrnametr.com.ua/knowledge',
            'https://metrnametr.com.ua/about',
        ];

        $this->assertSame($urls->count(), $urls->pluck('loc')->unique()->count());

        $urls->each(function ($url) use ($today) {
            $this->assertStringStartsWith('https://metrnametr.com.ua/', $url['loc']);
            $this->assertStringNotContainsString('?', $url['loc']);
            $this->assertLessThanOrEqual($today, $url['lastmod']);
            $this->assertRegExp('/^\\d{4}-\\d{2}-\\d{2}$/', $url['lastmod']);
        });

        $urls->whereIn('loc', $staticLocations)->each(function ($url) use ($today) {
            $this->assertNotSame($today, $url['lastmod']);
            $this->get(parse_url($url['loc'], PHP_URL_PATH))->assertStatus(200);
        });
    }

    public function testRivneLandingPagesDoNotClaimPhysicalStoreInRivne()
    {
        foreach (['/dveri-rivne', '/vkhidni-dveri-rivne', '/mizhkimnatni-dveri-rivne'] as $url) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('для клієнтів у Рівному', false)
                ->assertDontSee('магазин у Рівному');
        }
    }

    public function testRegionalLandingPagesExposeStrongInternalLinks()
    {
        foreach (['/dveri-volyn', '/dveri-rivne', '/vkhidni-dveri-rivne', '/mizhkimnatni-dveri-rivne'] as $url) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('Дивитися каталог дверей')
                ->assertSee('Вхідні двері у Луцьку')
                ->assertSee('Міжкімнатні двері у Луцьку')
                ->assertSee('Вхідні двері у Рівному')
                ->assertSee('Міжкімнатні двері у Рівному')
                ->assertSee('Двері у Волинській області')
                ->assertSee('Зв’язатися для консультації')
                ->assertSee('href="' . route('catalog') . '"', false)
                ->assertSee('href="/vkhidni-dveri-lutsk"', false)
                ->assertSee('href="/mizhkimnatni-dveri-lutsk"', false)
                ->assertSee('href="/vkhidni-dveri-rivne"', false)
                ->assertSee('href="/mizhkimnatni-dveri-rivne"', false)
                ->assertSee('href="/dveri-volyn"', false)
                ->assertSee('href="' . route('contacts') . '"', false);
        }
    }

    public function testRegionalLandingPagesHaveSubstantialLocalContentAndUseCases()
    {
        foreach (['dveri-volyn', 'dveri-rivne', 'vkhidni-dveri-rivne', 'mizhkimnatni-dveri-rivne'] as $slug) {
            $landing = SeoContent::landingPage($slug);
            $sectionBodies = array_map(function ($section) {
                return is_array($section) ? implode(' ', $section) : $section;
            }, $landing['sections']);
            $intro = is_array($landing['intro']) ? implode(' ', $landing['intro']) : $landing['intro'];

            $content = $intro
                . ' ' . implode(' ', array_keys($landing['sections']))
                . ' ' . implode(' ', $sectionBodies)
                . ' ' . implode(' ', array_map(function ($item) {
                    return $item['question'] . ' ' . $item['answer'];
                }, $landing['faq']));

            $this->assertGreaterThanOrEqual(4000, mb_strlen($content), $slug);
            $this->assertLessThanOrEqual(6000, mb_strlen($content), $slug);
            $this->assertStringContainsString('двері для квартири', mb_strtolower($content), $slug);
            $this->assertStringContainsString('для будинку', mb_strtolower($content), $slug);
            $this->assertStringContainsString('для офісу', mb_strtolower($content), $slug);
        }
    }

    public function testContactsPageHasPrimaryHeading()
    {
        $this->get(route('contacts'))
            ->assertStatus(200)
            ->assertSee('<h1>Контакти Метр на Метр</h1>', false);
    }

    public function testSiteLogoImagesHaveAltText()
    {
        $this->get('/dveri-rivne')
            ->assertStatus(200)
            ->assertSee('alt="Метр на Метр - двері у Луцьку та Волині"', false)
            ->assertSee('alt="Метр на Метр"', false);
    }

    public function testPublishedProductImagesReferenceExistingLocalFiles()
    {
        Product::published()->get()->each(function ($product) {
            $path = parse_url($product->cover, PHP_URL_PATH);

            if ($path && strpos($path, '/storage/products/') === 0) {
                $this->assertFileExists(public_path(ltrim($path, '/')), $product->cover);
            }
        });
    }

    public function testSeoLandingPagesAreIncludedInSitemapAndLlmsFiles()
    {
        $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->assertSee('<loc>https://metrnametr.com.ua/vkhidni-dveri-lutsk</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/mizhkimnatni-dveri-lutsk</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/dveri-volyn</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/dveri-rivne</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/vkhidni-dveri-rivne</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/mizhkimnatni-dveri-rivne</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/dveri-dlya-kvartyry</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/dveri-dlya-budynku</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/dveri-z-termorozryvom</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/protypozhezhni-dveri</loc>', false);

        $this->get('/llms.txt')
            ->assertStatus(200)
            ->assertSee('/vkhidni-dveri-lutsk')
            ->assertSee('/protypozhezhni-dveri');

        $this->get('/llms-full.txt')
            ->assertStatus(200)
            ->assertSee('/dveri-z-termorozryvom')
            ->assertSee('/mizhkimnatni-dveri-lutsk')
            ->assertSee('/dveri-rivne')
            ->assertSee('Метр на Метр знаходиться у Луцьку');
    }

    public function testProductMetaDescriptionUsesNameTypeCategoryAndPurpose()
    {
        $type = new Type(['title' => 'Вхідні двері']);
        $category = new Category(['title' => 'Терморозрив']);
        $category->setRelation('type', $type);

        $product = new Product([
            'title' => 'Арктика Люкс',
            'description' => '',
            'seo_description' => '',
        ]);
        $product->setRelation('categories', collect([$category]));

        $description = SeoContent::productMetaDescription($product);

        $this->assertStringContainsString('Арктика Люкс', $description);
        $this->assertStringContainsString('Вхідні двері', $description);
        $this->assertStringContainsString('Терморозрив', $description);
        $this->assertStringContainsString('для будинку', $description);
    }
}
