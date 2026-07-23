<?php

namespace App\Support;

use Illuminate\Support\Collection;

class RealWorks
{
    public static function page(): array
    {
        return config('real_works.page', []);
    }

    public static function filters(): array
    {
        return config('real_works.filters', []);
    }

    public static function cases(): Collection
    {
        return collect(config('real_works.cases', []));
    }

    public static function videos(): Collection
    {
        return collect(config('real_works.videos', []));
    }

    public static function featured(string $context): Collection
    {
        $ids = config("real_works.previews.{$context}", []);

        return collect($ids)
            ->map(function ($id) {
                return self::cases()->firstWhere('id', $id);
            })
            ->filter()
            ->values();
    }

    public static function allImages(): Collection
    {
        return self::cases()
            ->flatMap(function ($case) {
                return $case['images'];
            })
            ->values();
    }
}
