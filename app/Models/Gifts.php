<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gifts extends Model
{
    use HasFactory;

    protected $table = 'cat_gifts';

    protected $fillable = ['external_id', 'title', 'url'];

    protected $hidden = [
        'id',
        'created_at',
        'updated_at'
    ];
}
