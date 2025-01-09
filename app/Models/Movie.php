<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
        'imdb_title_id',
        'original_title',
        'year',
        'duration',
        'description',
        'director',
        'writers',
        'actors',
        'avg_vote',
        'votes',
        'production_company_id',
    ];

    public function genres()
    {
        return $this->belongsToMany(Genre::class, 'movie_genre', 'movie_id', 'genre_id');
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function likedMovies()
    {
        return $this->belongsToMany(
            Movie::class, Like::class, 'id', 'id'
        );
    }
}
