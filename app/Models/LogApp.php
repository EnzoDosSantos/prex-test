<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\User;

class LogApp extends Model
{
    use HasFactory;

    protected $table = 'log_app';

    protected $fillable = ['user_id', 'service', 'request', 'endpoint', 'status', 'response', 'method', 'ip'];

    protected $hidden = [
        'id',
        'user_id',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}
