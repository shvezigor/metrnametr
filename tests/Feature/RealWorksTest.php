<?php

namespace Tests\Feature;

use App\Support\RealWorks;
use Tests\TestCase;

class RealWorksTest extends TestCase
{
    public function testRealWorkContentHasFiveVerifiedCasesAndSafeLocations()
    {
        $cases = RealWorks::cases();
        $allowedLocations = ['Луцьк', 'Волинська область', 'для квартири', 'для приватного будинку'];

        $this->assertCount(5, $cases);
        $this->assertSame($cases->count(), $cases->pluck('id')->unique()->count());

        $cases->each(function ($case) use ($allowedLocations) {
            $this->assertNotEmpty($case['title']);
            $this->assertContains($case['location'], $allowedLocations);
            $this->assertNotEmpty($case['categories']);
            $this->assertNotEmpty($case['services']);
            $this->assertNotEmpty($case['images']);
            $this->assertNotEmpty($case['cta']);

            collect($case['images'])->each(function ($image) {
                $this->assertStringStartsWith('/images/real-works/', $image['jpg']);
                $this->assertStringEndsWith('.webp', $image['webp']);
                $this->assertGreaterThan(0, $image['width']);
                $this->assertGreaterThan(0, $image['height']);
                $this->assertNotEmpty($image['alt']);
            });
        });
    }

    public function testRealWorkPreviewContextsOnlyReturnConfiguredCases()
    {
        $this->assertCount(4, RealWorks::featured('home'));
        $this->assertCount(2, RealWorks::featured('vkhidni-dveri-lutsk'));
        $this->assertCount(3, RealWorks::featured('mizhkimnatni-dveri-lutsk'));
        $this->assertCount(3, RealWorks::featured('dveri-volyn'));
        $this->assertTrue(RealWorks::featured('unsupported-context')->isEmpty());
    }

    public function testEveryConfiguredRealWorkAssetExistsAndMatchesDimensions()
    {
        RealWorks::allImages()->each(function ($image) {
            $jpg = resource_path('client' . $image['jpg']);
            $webp = resource_path('client' . $image['webp']);

            $this->assertFileExists($jpg);
            $this->assertFileExists($webp);
            $this->assertSame([$image['width'], $image['height']], array_slice(getimagesize($jpg), 0, 2));
            $this->assertSame([$image['width'], $image['height']], array_slice(getimagesize($webp), 0, 2));
            $this->assertLessThan(350 * 1024, filesize($jpg));
            $this->assertLessThan(250 * 1024, filesize($webp));
        });
    }

    public function testOnlyTheApprovedDoorVideoAndItsPostersArePublished()
    {
        $this->assertCount(1, RealWorks::videos());

        RealWorks::videos()->each(function ($video) {
            $this->assertFileExists(resource_path('client' . $video['src']));
            $this->assertFileExists(resource_path('client' . $video['poster_jpg']));
            $this->assertFileExists(resource_path('client' . $video['poster_webp']));
        });

        $this->assertFileExists(resource_path('client' . RealWorks::page()['og_image']));
        $this->assertFileNotExists(resource_path('client/images/real-works/video-9.mp4'));
    }

    public function testRealWorksPageHasExactMetadataCanonicalAndContent()
    {
        $response = $this->get('/nashi-roboty')
            ->assertStatus(200)
            ->assertSee('<title>Реальні встановлення дверей у Луцьку та Волині | Метр на Метр</title>', false)
            ->assertSee('name="description" content="Приклади встановлених вхідних та міжкімнатних дверей у Луцьку й Волинській області. Замір, підбір, доставка та монтаж від Метр на Метр."', false)
            ->assertSee('<link rel="canonical" href="https://metrnametr.com.ua/nashi-roboty"', false)
            ->assertSee('<h1>Реальні встановлення дверей у Луцьку та Волині</h1>', false)
            ->assertSee('og:image', false)
            ->assertSee('https://metrnametr.com.ua/images/real-works/real-installations-og.jpg', false)
            ->assertSee('class="real-work-case"', false)
            ->assertSee('Відео з реальних робіт')
            ->assertDontSee('video (9).mp4', false);

        $this->assertSame(5, substr_count($response->getContent(), 'class="real-work-case"'));
    }

    public function testRealWorksPageEmitsBreadcrumbAndImageGallerySchema()
    {
        $content = $this->get('/nashi-roboty')->assertStatus(200)->getContent();

        $this->assertStringContainsString('"@type": "BreadcrumbList"', $content);
        $this->assertStringContainsString('"@type": "ImageGallery"', $content);
        $this->assertStringContainsString('"@type": "ImageObject"', $content);
        $this->assertStringContainsString('"contentUrl": "https://metrnametr.com.ua/images/real-works/', $content);
    }

    public function testRealWorksPicturesHaveFallbackDimensionsAndDeferredLoading()
    {
        $content = $this->get('/nashi-roboty')->assertStatus(200)->getContent();

        $this->assertStringContainsString('<source type="image/webp"', $content);
        $this->assertStringContainsString('width="960"', $content);
        $this->assertStringContainsString('height="1280"', $content);
        $this->assertStringContainsString('loading="lazy"', $content);
        $this->assertStringContainsString('decoding="async"', $content);
        $this->assertStringContainsString('data-video-src="/images/real-works/door-overview.mp4"', $content);
        $this->assertStringNotContainsString('<video src=', $content);
        $this->assertStringNotContainsString('<source src="/images/real-works/door-overview.mp4"', $content);
    }

    public function testHomepageShowsFourRealWorkPhotosAndWorksLink()
    {
        $response = $this->get('/')
            ->assertStatus(200)
            ->assertSee('Реальні встановлення дверей')
            ->assertSee('/nashi-roboty', false)
            ->assertSee('data-real-works-preview="home"', false);

        $this->assertSame(4, substr_count($response->getContent(), 'data-preview-work-image'));
    }

    public function testThreeLocalLandingsShowRelevantRealWorkProof()
    {
        $expected = [
            '/vkhidni-dveri-lutsk' => 2,
            '/mizhkimnatni-dveri-lutsk' => 3,
            '/dveri-volyn' => 3,
        ];

        foreach ($expected as $url => $count) {
            $response = $this->get($url)
                ->assertStatus(200)
                ->assertSee('Реальні встановлення дверей')
                ->assertSee('/nashi-roboty', false);

            $this->assertSame($count, substr_count($response->getContent(), 'data-preview-work-image'), $url);
        }
    }

    public function testRealWorksPreviewCtaUsesAnAllowedGa4Event()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('data-ga-event="real_works_click"', false)
            ->assertSee('data-cta-location="home_real_works"', false);

        $this->assertStringContainsString(
            "'real_works_click'",
            file_get_contents(resource_path('client/js/app.js'))
        );
    }

    public function testWorksPageIsDiscoverableInSitemapAndAiSources()
    {
        $sitemap = $this->get('/sitemap.xml')
            ->assertStatus(200)
            ->assertSee('<loc>https://metrnametr.com.ua/nashi-roboty</loc>', false)
            ->getContent();

        $this->assertStringNotContainsString('localhost', $sitemap);
        $this->assertStringNotContainsString('%20', $sitemap);
        preg_match_all('/<loc>(.*?)<\/loc>/', $sitemap, $locations);

        foreach ($locations[1] as $location) {
            $this->assertStringNotContainsString(' ', $location);
        }

        foreach (['/for-ai-agents', '/llms.txt', '/llms-full.txt', '/ai-policy.txt'] as $url) {
            $this->get($url)
                ->assertStatus(200)
                ->assertSee('/nashi-roboty', false);
        }
    }

    public function testSharedNavigationHasAccessibleNamesAndSizedCurrentLogo()
    {
        $content = $this->get('/nashi-roboty')->assertStatus(200)->getContent();

        $this->assertStringContainsString('aria-label="Відкрити навігацію"', $content);
        $this->assertStringContainsString('aria-label="Facebook"', $content);
        $this->assertStringContainsString('aria-label="Instagram"', $content);
        $this->assertStringContainsString('aria-label="YouTube"', $content);
        $this->assertStringContainsString(
            '<img src="/images/logo-2.png" width="309" height="360"',
            $content
        );
        $this->assertStringContainsString(
            'color: #666666;',
            file_get_contents(resource_path('client/scss/_common.scss'))
        );
        $this->assertStringContainsString(
            'height: auto;',
            file_get_contents(resource_path('client/scss/_header.scss'))
        );
        $this->assertStringContainsString(
            'height: auto;',
            file_get_contents(resource_path('client/scss/_common.scss'))
        );
    }
}
