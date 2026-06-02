<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vacancy extends Model
{
    protected $table = 'vacancies';
    public $timestamps = true;

    protected $fillable = [
        'title',
        'alias',
        'text',
        'contacts',
        'user_id',
        'salary',
        'published',
    ];

    public function scopePublished($query, $status = true) {
        return $query->where('published', '=', $status);
    }

    protected $appends = [
        'salary_text',
    ];

    public function user() {
        return $this->belongsTo(
            \App\Models\User::class,
            'user_id'
        );
    }

    public function getLocationAttribute() {
        return route('vacancy.show', ['alias' => $this->attributes['alias']]);
    }

    public function getSalaryTextAttribute() {
        return number_format($this->salary, 0, '.', ' ');
    }
}
