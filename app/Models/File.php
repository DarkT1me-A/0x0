<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable = [
        'hash', 'delete_hash', 'filename', 'expires_at',
    ];

    protected $dates = ['expires_at'];
}
