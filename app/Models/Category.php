<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected  $table = 'categories';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'seo_title',
        'seo_description',
        'canonical_url',
        'faq',
        'user_id',
        'catalog_id',
        'published',
        'type_id',
    ];

    public function scopePublished($query, $status = true) {
        return $query->where('published', '=', $status);
    }

    public function products() {
        return $this->belongsToMany('App\Models\Product', 'categories_has_products', 'category_id', 'product_id');
    }

    public function type() {
        return $this->belongsTo(
            \App\Models\Type::class,
            'type_id'
        );
    }

    public function user() {
        return $this->belongsTo(
            \App\Models\User::class,
            'user_id'
        );
    }

    public function catalog() {
        return $this->belongsTo(
            \App\Models\Catalog::class,
            'catalog_id'
        );
    }
}
