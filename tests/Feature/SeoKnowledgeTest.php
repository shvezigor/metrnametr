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
            ->assertSee('Оберіть двері за призначенням')
            ->assertSee('Двері в квартиру')
            ->assertSee('Для гуртових клієнтів');
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
        $this->assertNull(SeoContent::faqSchema([]));
        $this->assertSame('https://metrnametr.com.ua/images/og.jpg', SeoContent::ogImageFor((object) ['og_image' => '/images/og.jpg'], '/fallback.jpg'));
        $this->assertNotContains(null, SeoContent::defaultPageSchemas([SeoContent::faqSchema([])]));
    }

    public function testSeoLandingPagesRenderMetaCanonicalFaqAndSchemas()
    {
        $pages = [
            '/vkhidni-dveri-lutsk' => 'Вхідні двері Луцьк',
            '/mizhkimnatni-dveri-lutsk' => 'Міжкімнатні двері Луцьк',
            '/dveri-dlya-kvartyry' => 'Двері для квартири',
            '/dveri-dlya-budynku' => 'Двері для будинку',
            '/dveri-z-termorozryvom' => 'Двері з терморозривом',
            '/protypozhezhni-dveri' => 'Протипожежні двері',
        ];

        foreach ($pages as $url => $h1) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('<h1>' . $h1 . '</h1>', false)
                ->assertSee('<link rel="canonical" href="https://metrnametr.com.ua' . $url . '"', false)
                ->assertSee('FAQPage')
                ->assertSee('BreadcrumbList')
                ->assertSee('Популярні питання');
        }
    }

    public function testSeoLandingPagesAreIncludedInSitemapAndLlmsFiles()
    {
        $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->assertSee('<loc>https://metrnametr.com.ua/vkhidni-dveri-lutsk</loc>', false)
            ->assertSee('<loc>https://metrnametr.com.ua/mizhkimnatni-dveri-lutsk</loc>', false)
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
            ->assertSee('/mizhkimnatni-dveri-lutsk');
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
