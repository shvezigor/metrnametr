<?php

use App\Support\ProductCopy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NormalizeCategoryCopy extends Migration
{
    public function up()
    {
        $fields = ['title', 'seo_title', 'seo_description'];

        DB::table('categories')
            ->select(array_merge(['id'], $fields))
            ->orderBy('id')
            ->chunkById(100, function ($categories) use ($fields) {
                foreach ($categories as $category) {
                    $updates = [];

                    foreach ($fields as $field) {
                        $clean = ProductCopy::normalize($category->{$field});
                        if ($clean !== $category->{$field}) {
                            $updates[$field] = $clean;
                        }
                    }

                    if ($updates) {
                        DB::table('categories')->where('id', $category->id)->update($updates);
                    }
                }
            });
    }

    public function down()
    {
        // Known language errors must not be restored.
    }
}
