<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'products_sizes';
    public $timestamps = true;

    protected $fillable = [
        'title',
    ];

    public function products() {
        return $this->belongsToMany('App\Models\Product', 'products_has_products_sizes', 'products_size_id', 'product_id');
    }
}
