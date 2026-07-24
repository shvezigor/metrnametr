<?php

use App\Support\ProductCopy;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NormalizeRemainingMixedLanguageProductCopy extends Migration
{
    public function up()
    {
        $fields = ['title', 'text', 'description', 'seo_title', 'seo_description'];

        DB::table('products')
            ->select(array_merge(['id'], $fields))
            ->orderBy('id')
            ->chunkById(100, function ($products) use ($fields) {
                foreach ($products as $product) {
                    $updates = [];

                    foreach ($fields as $field) {
                        $clean = ProductCopy::normalize($product->{$field});
                        if ($clean !== $product->{$field}) {
                            $updates[$field] = $clean;
                        }
                    }

                    if ($updates) {
                        DB::table('products')->where('id', $product->id)->update($updates);
                    }
                }
            });
    }

    public function down()
    {
        // Known language errors must not be restored.
    }
}
