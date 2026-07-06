<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSeoContentFields extends Migration
{
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('keywords');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('canonical_url')->nullable()->after('seo_description');
            $table->string('og_image')->nullable()->after('canonical_url');
            $table->longText('faq')->nullable()->after('og_image');
            $table->longText('extra_fields')->nullable()->after('faq');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('title');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('canonical_url')->nullable()->after('seo_description');
            $table->longText('faq')->nullable()->after('canonical_url');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->string('seo_title')->nullable()->after('keywords');
            $table->text('seo_description')->nullable()->after('seo_title');
            $table->string('canonical_url')->nullable()->after('seo_description');
            $table->string('og_image')->nullable()->after('canonical_url');
            $table->longText('faq')->nullable()->after('og_image');
        });
    }

    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'canonical_url',
                'og_image',
                'faq',
                'extra_fields',
            ]);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'canonical_url',
                'faq',
            ]);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn([
                'seo_title',
                'seo_description',
                'canonical_url',
                'og_image',
                'faq',
            ]);
        });
    }
}
