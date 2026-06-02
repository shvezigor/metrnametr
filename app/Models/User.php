<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['avatar'];

    public function vacancies() {
        return $this->hasMany(Vacancy::class, 'user_id');
    }

    public function articles() {
        return $this->hasMany(Article::class, 'user_id');
    }

    public function products() {
        return $this->hasMany(Product::class, 'user_id');
    }

    public function catalog() {
        return $this->hasMany(Catalog::class, 'user_id');
    }

    public function categories() {
        return $this->hasMany(Category::class, 'user_id');
    }

    public function getAvatarAttribute() {
        return sprintf('%s/images/placeholder-user.svg', config('app.url'));
    }
}
