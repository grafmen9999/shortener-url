<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShortUrl extends Model
{
    protected $fillable = [
        'short_code',
        'long_url',
        'created_at'
    ];

    public $timestamps = false;
}
