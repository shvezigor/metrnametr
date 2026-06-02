<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Type extends Model
{
    protected $table = 'types';
    public $timestamps = true;

    protected $fillable = [
        'title',
    ];

    public function categories() {
        return $this->hasMany('App\Models\Category', 'type_id');
    }

    public function getLocationAttribute() {
        $categories = $this
            ->categories()
            ->published()
            ->pluck('id');

        $preparedArray = [];

        foreach ($categories as $item) {
            $preparedArray['categories[' . $item . ']'] = $item;
        }

        return route('catalog', $preparedArray);
    }
}
