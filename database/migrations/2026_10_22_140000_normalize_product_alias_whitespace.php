<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class NormalizeProductAliasWhitespace extends Migration
{
    public function up()
    {
        DB::table('products')
            ->select(['id', 'alias'])
            ->where('alias', 'like', '% %')
            ->orderBy('id')
            ->chunkById(100, function ($products) {
                foreach ($products as $product) {
                    $normalized = preg_replace('/\s+/u', '-', trim($product->alias));

                    if ($normalized === $product->alias) {
                        continue;
                    }

                    $collision = DB::table('products')
                        ->where('alias', $normalized)
                        ->where('id', '<>', $product->id)
                        ->exists();

                    if (!$collision) {
                        DB::table('products')->where('id', $product->id)->update([
                            'alias' => $normalized,
                        ]);
                    }
                }
            });
    }

    public function down()
    {
        // Canonical product URLs must not be changed back to aliases with spaces.
    }
}
