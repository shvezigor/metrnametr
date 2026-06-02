<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use Faker\Generator as Faker;
use App\Models\Category;
use App\Models\User;
use App\Models\Catalog;

$factory->define(Category::class, function (Faker $faker) {

    $title = $faker->sentence(rand(3, 8), true);

    return [
        'title' => $title,
        'user_id' => function () {
            return factory(User::class)->create()->id;
        },
        'catalog_id' => function () {
            return factory(Catalog::class)->create()->id;
        }
    ];
});
