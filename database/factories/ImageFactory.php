<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;

$factory->define(App\Models\Image::class, function (Faker $faker) {

    return [
        'location' => $faker->imageUrl(640,480, null, false),
        'product_id' => function () {
            return factory(App\Models\Product::class)->create()->id;
        }
    ];
});
