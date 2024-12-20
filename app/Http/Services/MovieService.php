<?php

namespace App\Http\Services;

use App\Http\Resources\MovieResource;
use App\Http\Resources\MoviesCollection;
use App\Models\Movie;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use App\Http\Dtos\FilterDto;

class MovieService
{

    public function getMovies(FilterDto $filter)
    {
        $query = DB::table('movies')
            ->leftJoin('movie_genre', 'movie_genre.movie_id', '=', 'movies.id')
            ->leftJoin('genres', 'movie_genre.genre_id', '=', 'genres.id')
            ->leftJoin('production_companies', 'movies.production_company_id', '=', 'production_companies.id') // Join for production_company
            ->select(
                'movies.*',
                'production_companies.name as production_company', // Fetch production company name
                DB::raw('GROUP_CONCAT(DISTINCT genres.name ORDER BY genres.name SEPARATOR ",") as genres_list')
            )
            ->groupBy('movies.id');

        if (!empty($filter->q)) {
            $query->where('movies.original_title', 'like', '%' . $filter->q . '%');
        }

        if (!empty($filter->genres)) {
            $query->whereIn('genres.name', $filter->genres);
        }

        return MovieResource::collection($query->paginate(10))->response()->getData();

    }

    public function getMovie(int $id)
    {
        $query = DB::table('movies')
            ->where('movies.id', '=', $id)
            ->leftJoin('movie_genre', 'movie_genre.movie_id', '=', 'movies.id')
            ->leftJoin('genres', 'movie_genre.genre_id', '=', 'genres.id')
            ->leftJoin('production_companies', 'movies.production_company_id', '=', 'production_companies.id') // Join for production_company
            ->leftJoin('comments', 'comments.movie_id', '=', 'movies.id') // Join for comments
            ->leftJoin('users', 'comments.user_id', '=', 'users.id') // Join for comment authors
            ->select(
                'movies.*',
                'production_companies.name as production_company', // Fetch production company name
                DB::raw('GROUP_CONCAT(DISTINCT genres.name ORDER BY genres.name SEPARATOR ",") as genres_list'),
                DB::raw('GROUP_CONCAT(DISTINCT comments.comment ORDER BY comments.created_at SEPARATOR "||") as comments_list'), // Concatenate comments
                DB::raw('GROUP_CONCAT(DISTINCT users.name ORDER BY comments.created_at SEPARATOR "||") as commenters_list') // Concatenate comment authors
            )
            ->groupBy('movies.id');

        $movie = $query->first();

        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        // Split concatenated comments and commenters
        $movie->comments = [];
        if (!empty($movie->comments_list)) {
            $comments = explode('||', $movie->comments_list);
            $commenters = explode('||', $movie->commenters_list);
            foreach ($comments as $key => $comment) {
                $movie->comments[] = [
                    'comment' => $comment,
                    'user' => $commenters[$key] ?? 'Anonymous',
                ];
            }
        }

        unset($movie->comments_list, $movie->commenters_list);

        return MovieResource::make($movie)->response()->getData();
    }


}
