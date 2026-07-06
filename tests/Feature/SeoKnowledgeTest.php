<?php

namespace Tests\Feature;

use Tests\TestCase;

class SeoKnowledgeTest extends TestCase
{
    public function testLlmsTxtDescribesSiteAndReferencesKeyResources()
    {
        $response = $this->get('/llms.txt');

        $response->assertStatus(200);
        $response->assertSee('Метр на Метр');
        $response->assertSee('https://metrnametr.com.ua');
        $response->assertSee('/catalog');
        $response->assertSee('/knowledge');
        $response->assertSee('/sitemap.xml');
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
            ->assertSee('Порівняння');
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
            ->assertSee('<changefreq>weekly</changefreq>', false);
    }
}
