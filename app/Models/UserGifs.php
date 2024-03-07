<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Gifs;

class UserGifs extends Model
{
    use HasFactory;

    protected $table = 'prex_user_gifs';
    protected $fillable = [
        'user_id',
        'gif_id',
        'alias'
    ];

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at',
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function gif()
    {
        return $this->hasOne(Gifs::class, 'id', 'gif_id');
    }
}
