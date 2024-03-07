<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use App\Models\UserGifs;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $table = 'prex_user';
    protected $fillable = [
        'email',
        'password'
    ];

    protected $hidden = [
        'id',
        'password',
        'created_at',
        'updated_at',
    ];

    public function gifs()
    {
        return $this->hasMany(UserGifs::class, 'user_id', 'id');
    }
}
