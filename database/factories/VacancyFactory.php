<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\Vacancy;
use App\Models\User;

$factory->define(Vacancy::class, function (Faker $faker) {

    $title = $faker->sentence(rand(3, 8), true);

    while (Vacancy::where('alias', Str::slug($title))->exists()) {
        $title = $faker->sentence(rand(3, 8), true);
    }

    return [
        'title' => $title,
        'alias' => Str::slug($title),
        'text' => $faker->paragraph,
        'salary' => $faker->numberBetween(10, 20) * 100,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        }
    ];
});
