<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    public $timestamps = true;

    const LABEL_EMPTY = 0;
    const LABEL_NEW = 1;
    const LABEL_ACTION = 2;
    const LABEL_TOP_SALE = 3;

    protected $fillable = [
        'title',
        'alias',
        'text',
        'price',
        'user_id',
        'keywords',
        'description',
        'label',
        'published',
        'slider',
    ];

    protected $appends = [
        'price_text',
        'cover',
        'label_text',
        'label_class',
    ];

    public function scopePublished($query, $status = true) {
        return $query->where('published', '=', $status);
    }

    public function scopeSlider($query, $status = true) {
        return $query->where('slider', '=', $status);
    }

    public function categories() {
        return $this->belongsToMany('App\Models\Category', 'categories_has_products', 'product_id', 'category_id');
    }

    public function sizes() {
        return $this->belongsToMany('App\Models\Size', 'products_has_products_sizes', 'product_id', 'products_size_id');
    }

    public function user() {
        return $this->belongsTo(
            \App\Models\User::class,
            'user_id'
        );
    }

    public function scopeLastPublishedCatalog() {
        return $this->categories()->where('published', 1)->whereHas('catalog', function($q) {
            $q->where('published',1);
        })->orderBy('catalog_id','desc')->first()->catalog()->first();
    }

    public function scopeFirstPublishedCatalog() {
        return $this->categories()->where('published', 1)->whereHas('catalog', function($q) {
            $q->where('published',1);
        })->orderBy('catalog_id','asc')->first()->catalog()->first();
    }

    public function getPriceTextAttribute() {
        return sprintf('%d ₴', $this->price);
    }

    public function getCoverAttribute() {

        if (!$this->images()->get()->isEmpty()) {
            return $this->images()->first()->location;
        }

        return sprintf('%s/images/placeholder-product.png', config('app.url'));
    }

    public function getLabelTextAttribute() {
        return self::getLabels($this->label);
    }

    public function getLabelClassAttribute() {
        return self::getLabelClasses($this->label);
    }

    static public function getLabels($index = null) {
        $arr = [
            self::LABEL_EMPTY => 'None',
            self::LABEL_NEW => 'Новинка',
            self::LABEL_ACTION => 'Акція',
            self::LABEL_TOP_SALE => 'ТОП',
        ];

        if ($index !== null) {
            return isset($arr[$index]) ? $arr[$index] : '';
        }

        return $arr;
    }

    static public function getLabelClasses($index = null) {
        $arr = [
            self::LABEL_EMPTY => '',
            self::LABEL_NEW => 'novelty',
            self::LABEL_ACTION => 'action',
            self::LABEL_TOP_SALE => 'top-sales',
        ];

        if ($index !== null) {
            return isset($arr[$index]) ? $arr[$index] : '';
        }

        return $arr;
    }

    public function getLocationAttribute() {
        return route('product.show', ['alias' => $this->attributes['alias']]);
    }

    public function images() {
        return $this->hasMany(Image::class, 'product_id');
    }

    // BEGIN For share
    public function getTwitterShareLinkAttribute()
    {
        return sprintf('http://twitter.com/intent/tweet?text=%s&url=%s', $this->text, $this->location);
    }

    public function getFacebookShareLinkAttribute()
    {
        return sprintf(
            'https://www.facebook.com/sharer/sharer.php?u=%s&picture=%s&title=%s&quote=%s&description=%s',
            urlencode($this->location),
            urlencode($this->cover),
            urlencode($this->title),
            urlencode($this->title),
            urlencode($this->description)
        );
    }

    public function getTelegramShareLinkAttribute() {
        return sprintf('https://t.me/share/url?url=%s&text=%s', rawurlencode($this->location), rawurlencode($this->title));
    }
    // END For share
}
