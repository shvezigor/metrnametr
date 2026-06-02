<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateArticleAndProductsForPublishedTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog', function (Blueprint $table) {
            $table->tinyInteger('published')->default(0);
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->tinyInteger('published')->default(0);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->tinyInteger('published')->default(0);
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->tinyInteger('published')->default(0);
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->tinyInteger('published')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog', function (Blueprint $table) {
            $table->removeColumn('published');
        });

        Schema::table('categories', function (Blueprint $table) {
            $table->removeColumn('published');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->removeColumn('published');
        });

        Schema::table('articles', function (Blueprint $table) {
            $table->removeColumn('published');
        });

        Schema::table('vacancies', function (Blueprint $table) {
            $table->removeColumn('published');
        });
    }
}
