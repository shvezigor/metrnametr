<?php

namespace Tests\Feature;

use App\Support\SeoContent;
use Tests\TestCase;

class KnowledgePublicationTest extends TestCase
{
    public function testTwoEditorialGuidesAreIndexableDiscoverableAndListedOnceInSitemap()
    {
        $slugs = [
            'kondensat-na-vkhidnykh-dveriakh',
            'yak-vybraty-mizhkimnatni-dveri-dlia-kvartyry',
        ];

        $this->assertCount(12, SeoContent::indexableArticles());
        $index = $this->get('/knowledge')->assertStatus(200);
        $sitemap = $this->get('/sitemap.xml')->assertStatus(200)->getContent();

        foreach ($slugs as $slug) {
            $article = SeoContent::article($slug);
            $path = '/knowledge/' . $slug;
            $canonical = 'https://metrnametr.com.ua' . $path;

            $this->assertTrue(SeoContent::isIndexableArticle($article));
            $this->assertCount(1, SeoContent::articles()->where('slug', $slug));
            $this->assertSame([], SeoContent::articleLongFormBlocks($article));
            $index->assertSee($path, false);
            $response = $this->get($path)->assertStatus(200)
                ->assertSee('<title>' . $article['seo_title'] . '</title>', false)
                ->assertSee('<link rel="canonical" href="' . $canonical . '"', false)
                ->assertDontSee('noindex', false)
                ->assertSee('alt="' . $article['image_alt'] . '"', false)
                ->assertDontSee('Практичний алгоритм вибору')
                ->assertSee('id="comparison"', false);
            $html = $response->getContent();

            $this->assertSame(1, preg_match_all('/<h1\b/i', $html));
            $this->assertSame(1, substr_count($sitemap, '<loc>' . $canonical . '</loc>'));
            $this->assertFalse($response->headers->has('X-Robots-Tag'));

            preg_match_all('/<script type="application\/ld\+json">(.*?)<\/script>/s', $html, $scripts);
            $schemas = array_map(function ($json) {
                $schema = json_decode($json, true);
                $this->assertSame(JSON_ERROR_NONE, json_last_error());

                return $schema;
            }, $scripts[1]);
            $schema = collect($schemas)->firstWhere('@type', 'Article');
            $this->assertNotEmpty($schema);
            $this->assertSame($canonical, $schema['mainEntityOfPage']);
            $this->assertSame($article['title'], $schema['headline']);

            foreach (array_keys($article['sections']) as $heading) {
                $id = \Illuminate\Support\Str::slug($heading);
                $response->assertSee('href="#' . $id . '"', false)
                    ->assertSee('id="' . $id . '"', false);
            }
        }

        $draftPath = '/knowledge/dveri-dlia-ofisu-yak-obraty';
        $this->get($draftPath)->assertStatus(200)->assertSee('noindex,follow', false);
        $this->assertStringNotContainsString('<loc>https://metrnametr.com.ua' . $draftPath . '</loc>', $sitemap);
    }

    public function testEditorialGuidesRenderTopicSpecificContentListsAndLinks()
    {
        $this->get('/knowledge/kondensat-na-vkhidnykh-dveriakh')
            ->assertStatus(200)
            ->assertSee('точку роси')
            ->assertSee('Не використовуйте відкритий вогонь')
            ->assertSee('<ol>', false)
            ->assertSee('Конденсат, протяг чи протікання')
            ->assertSee('href="/knowledge/termo-rozryv-shcho-tse"', false)
            ->assertSee('https://www.velux.ua/help-and-support/wiki/window-condensation-ua', false);

        $this->get('/knowledge/yak-vybraty-mizhkimnatni-dveri-dlia-kvartyry')
            ->assertStatus(200)
            ->assertSee('Розмір полотна не дорівнює розміру отвору')
            ->assertSee('Чеклист перед оплатою')
            ->assertSee('У кошторисі перелічені коробка')
            ->assertSee('href="/mizhkimnatni-dveri-lutsk"', false)
            ->assertSee('<th scope="col">Елемент</th>', false);

        $links = SeoContent::articleCommercialLinks(SeoContent::article('yak-vybraty-mizhkimnatni-dveri-dlia-kvartyry'));
        $this->assertSame([
            '/mizhkimnatni-dveri-lutsk',
            '/mizhkimnatni-dveri-z-montazhem-lutsk',
        ], $links->pluck('path')->all());
    }
}
