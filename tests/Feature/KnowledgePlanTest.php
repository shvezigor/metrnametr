<?php

namespace Tests\Feature;

use App\Support\KnowledgePlan;
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
}
