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
}
