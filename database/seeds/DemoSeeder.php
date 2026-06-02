<?php

use Illuminate\Database\Seeder;

class DemoSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);
        $this->call(DatabaseSeeder::class);

        // Create users
        factory(App\Models\User::class, 3)->create()->each(
            function ($user) {

                // Create news
                factory(App\Models\Article::class, rand(5, 10))->create(
                    [
                        'user_id' => $user->id,
                    ]
                );

                // Create vacancies
                factory(App\Models\Vacancy::class, rand(1, 3))->create(
                    [
                        'user_id' => $user->id,
                    ]
                );

                factory(App\Models\Catalog::class, 1)->create(
                    [
                        'user_id' => $user->id,
                    ]
                )->each(
                    function ($catalog) use ($user) {
                        factory(App\Models\Category::class, 3)->create(
                            [
                                'user_id' => $user->id,
                                'catalog_id' => $catalog->id,
                            ]
                        )->each(
                            function ($category) use ($user) {
                                // Create products

                                $products = factory(App\Models\Product::class, 10)->create(
                                    [
                                        'user_id' => $user->id,
                                    ]
                                )->each(function ($product) {
                                    factory(App\Models\Image::class, rand(2, 5))->create([
                                        'product_id' => $product->id,
                                    ]);
                                });

                                $category->products()->attach($products);
                            }
                        );
                    }
                );
            }
        );
    }
}
