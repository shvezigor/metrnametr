<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    protected $table = 'articles';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'alias',
        'text',
        'cover',
        'user_id',
        'keywords',
        'description',
        'published',
    ];

    public function scopePublished($query, $status = true) {
        return $query->where('published', '=', $status);
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getCoverAttribute($value) {

        if (!empty($value)) {
            return $value;
        }

        return sprintf('%s/images/placeholder-article.jpg', config('app.url'));
    }

    public function getLocationAttribute() {
        return route('article.show', ['alias' => $this->attributes['alias']]);
    }

    // BEGIN For share
    public function getTwitterShareLinkAttribute()
    {
        return sprintf('http://twitter.com/intent/tweet?text=%s&url=%s', $this->description, $this->location);
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
