<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;

/**
 * @mixin Builder
 */
class Like extends Model
{
    protected $fillable = [
        'user_id',
        'movie_id'
    ];
}
