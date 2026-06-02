<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */
use Faker\Generator as Faker;
use Illuminate\Support\Str;
use App\Models\Article;
use App\Models\User;

$factory->define(Article::class, function (Faker $faker) {

    $title = $faker->sentence(rand(3, 8), true);

    while (Article::where('alias', Str::slug($title))->exists()) {
        $title = $faker->sentence(rand(3, 8), true);
    }

    return [
        'title' => $title,
        'alias' => Str::slug($title),
        'text' => $faker->paragraph,
        'cover' => $faker->imageUrl(640, 640),
        'user_id' => function () {
            return factory(User::class)->create()->id;
        }
    ];
});
