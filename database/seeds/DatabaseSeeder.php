<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UserSeeder::class);

        // BEGIN Main Admin
        $user = App\Models\User::create([
            'id' => 1,
            'name' => 'Василь Пупко',
            'email' => 'admin@admin.com',
            'password' => bcrypt('secret'),
            'email_verified_at' => now(),
        ]);

        $slider = [
//            [
//                'label' => '',
//                'title' => '',
//                'text' => '',
//                'button' => '',
//                'link' => '',
//                'image' => '',
//            ],
        ];

        DB::table('settings')->insert([
            ['key' => 'facebook', 'type' => \App\Models\Setting::TYPE_STRING, 'value' => null],
            ['key' => 'instagram', 'type' => \App\Models\Setting::TYPE_STRING, 'value' => null],
            ['key' => 'youtube', 'type' => \App\Models\Setting::TYPE_STRING, 'value' => null],
            ['key' => 'telegram', 'type' => \App\Models\Setting::TYPE_STRING, 'value' => null],
            ['key' => 'phones', 'type' => \App\Models\Setting::TYPE_STRING, 'value' => '(0332) 78 46 77, (0332) 78 93 00, (067) 334 33 68'],
            ['key' => 'main_title', 'type' => \App\Models\Setting::TYPE_STRING, 'value' => 'Про нас'],
            [
                'key' => 'main_text',
                'type' => \App\Models\Setting::TYPE_STRING,
                'value' => 'Компанія по продажу та виробництву дверей "Метр на Метр" пропонує нашим клієнтам широкий вибір міжкімнатних та вхідних дверей оптом і в роздріб. Широкий вибір дверей які зараз є на ринку, ставить нас перед вибором, з якого матеріалу вибрати двері, якому стилю віддати перевагу, авангардний або класичний стиль? Красиві двері, які правильно обрані відповідно до загальної концепції дизайну приміщення, можуть стати головною окрасою вашого інтер\'єру, його змістовим центром. Ми дорожимо своїм ім\'ям і репутацією, намагаємося будувати відносини з нашими клієнтами та партнерами на принципах глибокої поваги і дотримання покладених на себе зобов\'язань.'
            ],
            ['key' => 'address', 'type' => \App\Models\Setting::TYPE_STRING, 'value' => 'м. Луцьк, пр. Перемоги, 24'],
            ['key' => 'email', 'type' => \App\Models\Setting::TYPE_STRING, 'value' => 'metrnametr@ukr.net'],
            [
                'key' => 'slider', 
                'type' => \App\Models\Setting::TYPE_ARRAY, 
                'value' => json_encode($slider),
            ],
            [
                'key' => 'meta_title', 
                'type' => \App\Models\Setting::TYPE_STRING, 
                'value' => 'Інтернет-магазин дверей | Метр на Метр | Продаж дверей',
            ],
            [
                'key' => 'meta_description', 
                'type' => \App\Models\Setting::TYPE_STRING, 
                'value' => 'Інтернет магазин по продажу дверей з мдф накладками від фірми \"Метр на Метр\". Двері власного виробництва MetrDoor. В наявності великий асортимент дверей.',
            ],
            [
                'key' => 'meta_keywords', 
                'type' => \App\Models\Setting::TYPE_STRING, 
                'value' => 'Інтернет-магазин дверей, Метр на Метр, Продаж дверей з мдф накладками',
            ],
        ]);
    }
}
