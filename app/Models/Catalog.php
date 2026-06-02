<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Catalog extends Model
{
    protected $table = 'catalog';

    public $timestamps = true;

    protected $fillable = [
        'title',
        'alias',
        'user_id',
        'published',
    ];

    public function scopePublished($query, $status = true) {
        return $query->where('published', '=', $status);
    }

    public function user() {
        return $this->belongsTo(
            \App\Models\User::class,
            'user_id'
        );
    }

    public function categories() {
        return $this->hasMany(Category::class, 'catalog_id');
    }

    public function getProductAttribute() {
        if ($this->categories()->published()->count() === 0) {
            return false;
        }

        $categoriesIDs = $this->categories()->published()->pluck('id');

        $product = Product::published()->whereHas('categories', function($q) use ($categoriesIDs) {
            $q->whereIn('id', $categoriesIDs);
        })->first();

        return $product ?? false;
    }
}
