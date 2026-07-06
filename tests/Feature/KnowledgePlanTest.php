<?php

namespace Tests\Feature;

use App\Support\KnowledgePlan;
use App\Support\SeoContent;
use Tests\TestCase;

class KnowledgePlanTest extends TestCase
{
    public function testKnowledgePlanContainsOneHundredUniqueArticlesWithRequiredDistribution()
    {
        $articles = KnowledgePlan::articles();

        $this->assertCount(100, $articles);
        $this->assertCount(100, $articles->pluck('slug')->unique());

        $this->assertSame([
            'choice' => 20,
            'production' => 20,
            'installation' => 20,
            'security' => 10,
            'design' => 10,
            'care' => 10,
            'faq' => 10,
        ], $articles->countBy('cluster')->all());

        $articles->each(function ($article) {
            $this->assertNotEmpty($article['title']);
            $this->assertNotEmpty($article['slug']);
            $this->assertNotEmpty($article['meta_title']);
            $this->assertNotEmpty($article['meta_description']);
            $this->assertNotEmpty($article['h1']);
            $this->assertCount(10, $article['sections']);
            $this->assertCount(6, $article['image_prompts']);
            $this->assertCount(10, $article['faq_questions']);
        });
    }

    public function testKnowledgePlanPageRendersArticleRoadmap()
    {
        $this->get('/knowledge/plan')
            ->assertStatus(200)
            ->assertSee('План бази знань')
            ->assertSee('100')
            ->assertSee('Вибір дверей')
            ->assertSee('Технології виробництва');
    }

    public function testKnowledgeIndexRendersPublishedArticlesNotPlannedStatuses()
    {
        $publishedArticles = SeoContent::articles();

        $this->assertGreaterThanOrEqual(100, $publishedArticles->count());
        $this->assertSame($publishedArticles->count(), $publishedArticles->pluck('slug')->unique()->count());

        $this->get('/knowledge')
            ->assertStatus(200)
            ->assertSee('/knowledge/yak-vybraty-mizhkimnatni-dveri-dlia-kvartyry')
            ->assertSee('/knowledge/dveri-dlia-ofisu-yak-obraty')
            ->assertDontSee('Заплановано');

        $this->get('/knowledge/yak-vybraty-mizhkimnatni-dveri-dlia-kvartyry')
            ->assertStatus(200)
            ->assertSee('Як вибрати міжкімнатні двері для квартири')
            ->assertSee('FAQPage')
            ->assertSee('Порівняння');

        $this->get('/knowledge/yak-pidhotuvatysia-do-zamovlennia-dverei')
            ->assertStatus(200)
            ->assertSee('Як підготуватися до замовлення дверей')
            ->assertSee('FAQPage')
            ->assertSee('Порівняння');
    }

    public function testKnowledgeIndexIsPaginatedAndUsesArticleImages()
    {
        $response = $this->get('/knowledge');

        $response->assertStatus(200);
        $response->assertSee('/knowledge?page=2', false);
        $response->assertSee('/knowledge/yak-vybraty-vkhidni-dveri-dlia-kvartyry/image.svg', false);
        $response->assertSee('loading="lazy"', false);
        $response->assertDontSee('/knowledge/yak-pidhotuvatysia-do-zamovlennia-dverei');

        $this->get('/knowledge?page=2')
            ->assertStatus(200)
            ->assertSee('/knowledge?page=1', false);

        $this->get('/knowledge/yak-pidhotuvatysia-do-zamovlennia-dverei')
            ->assertStatus(200)
            ->assertSee('/knowledge/yak-pidhotuvatysia-do-zamovlennia-dverei/image.svg', false)
            ->assertSee('alt="Як підготуватися до замовлення дверей"', false)
            ->assertSee('width="1200"', false)
            ->assertSee('height="675"', false);
    }

    public function testKnowledgeArticleImageEndpointReturnsSvg()
    {
        $this->get('/knowledge/yak-pidhotuvatysia-do-zamovlennia-dverei/image.svg')
            ->assertStatus(200)
            ->assertHeader('Content-Type', 'image/svg+xml; charset=UTF-8')
            ->assertSee('<svg', false)
            ->assertSee('Метр на Метр', false);
    }
}
