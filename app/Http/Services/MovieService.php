<?php

namespace App\Http\Services;

use App\Http\Resources\MovieResource;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use App\Http\Dtos\FilterDto;

class MovieService
{


    /**
     * @param Collection $movies
     * @return array
     */
    public function getFilters(Collection $movies): array
    {

        $uniqueGenres = $movies->pluck('genres_list')
            ->map(function ($genres) {
                return explode(',', $genres);
            })
            ->flatten()
            ->unique()
            ->values()
            ->toArray();

        return [
            'genres' => $uniqueGenres,
        ];
    }

    /**
     * @param FilterDto $filter
     * @return Builder
     */
    public function getMovies(FilterDto $filter): Builder
    {
        $query = DB::table('movies')
            ->leftJoin('movie_genre', 'movie_genre.movie_id', '=', 'movies.id')
            ->leftJoin('genres', 'movie_genre.genre_id', '=', 'genres.id')
            ->leftJoin('production_companies', 'movies.production_company_id', '=', 'production_companies.id') // Join for production_company
            ->select(
                'movies.*',
                'production_companies.name as production_company',
                DB::raw('GROUP_CONCAT(DISTINCT genres.name ORDER BY genres.name SEPARATOR ",") as genres_list')
            )
            ->groupBy('movies.id');

        if (!empty($filter->q)) {
            $query->where('movies.original_title', 'like', '%' . $filter->q . '%');
        }

        if (!empty($filter->genres)) {
            $query->whereExists(function ($subquery) use ($filter) {
                $subquery->select(DB::raw(1))
                    ->from('movie_genre as mg_filter')
                    ->join('genres as g_filter', 'mg_filter.genre_id', '=', 'g_filter.id')
                    ->whereRaw('mg_filter.movie_id = movies.id')
                    ->whereIn('g_filter.name', $filter->genres);
            });
        }

        return $query;

    }

    /**
     * @param int $id
     * @return JsonResponse|mixed
     */
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

        $movie->comments = new Collection();
        if (!empty($movie->comments_list)) {
            $comments = explode('||', $movie->comments_list);
            $commenters = explode('||', $movie->commenters_list);
            $index = 0;
            foreach ($comments as $key => $comment) {
                $movie->comments->add([
                    'id' => $index,
                    'comment' => $comment,
                    'user' => $commenters[$key] ?? 'Anonymous'
                ]);

                $index++;
            }
        }

        unset($movie->comments_list, $movie->commenters_list);

        return MovieResource::make($movie)->response()->getData();
    }


}
