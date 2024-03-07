<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Gifts;

class UserGifts extends Model
{
    use HasFactory;

    protected $table = 'prex_user_gifts';
    protected $fillable = [
        'user_id',
        'gift_id',
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

    public function gift()
    {
        return $this->hasOne(Gifts::class, 'id', 'gift_id');
    }
}
