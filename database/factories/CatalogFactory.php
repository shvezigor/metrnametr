<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\Catalog;
use App\Models\User;

$factory->define(Catalog::class, function (Faker $faker) {

    $title = $faker->sentence(rand(3, 8), true);

    while (Catalog::where('alias', Str::slug($title))->exists()) {
        $title = $faker->sentence(rand(3, 8), true);
    }

    return [
        'title' => $title,
        'alias' => Str::slug($title),
        'user_id' => function () {
            return factory(User::class)->create()->id;
        }
    ];
});
