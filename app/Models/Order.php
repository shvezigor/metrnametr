<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = 'orders';
    public $timestamps = true;

    protected $fillable = [
        'name',
        'phone',
        'product_id'
    ];

    public function product() {
        return $this->belongsTo(Product::class, 'product_id');
    }
}
