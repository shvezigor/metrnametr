<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    protected $table = 'images';
    public $timestamps = true;

    protected $fillable = [
        'product_id',
        'location'
    ];

    public function product() {
        return $this->belongsTo(
            \App\Models\Product::class,
            'product_id'
        );
    }
}
