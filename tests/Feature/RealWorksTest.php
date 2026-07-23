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
}
