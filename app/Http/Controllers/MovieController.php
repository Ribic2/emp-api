<?php

namespace App\Http\Controllers;

use App\Http\Dtos\FilterDto;
use App\Http\Resources\MovieResource;
use App\Http\Services\MovieService;
use App\Models\Like;
use App\Models\LikeAndFavourite;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MovieController extends Controller
{
    public function __construct(
        public MovieService $movieService
    )
    {
    }

    public function getRecommendedMovies(): JsonResponse
    {

        return response()->json($this->movieService->getLikedAndFavouriteMovies());
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function movies(Request $request): JsonResponse
    {
        $filter = new FilterDto(
            $request->input('q'),
            $request->input('genres'),
            $request->input('rating'),
        );
        $movies = $this->movieService->getMovies($filter);
        $newFilters = $this->movieService->getFilters($movies->get());

        return response()->json([
            'data' => MovieResource::collection($movies->paginate(10)),
            'filters' => [
                'applied' => $filter,
                'available' => $newFilters,
            ],
        ]);
    }

    public function movie(int $id): JsonResponse
    {
        $movie = $this->movieService->getMovie($id);
        return response()->json($movie);
    }

    public function like(int $id): JsonResponse
    {

        $isLiked = Like::where([
            'movie_id' => $id,
            'user_id' => Auth::id()
        ]);

        if($isLiked->exists()){
            $isLiked->delete();
        }

        else{
            Like::create([
                'movie_id' => $id,
                'user_id' => Auth::id()
            ]);
        }
        return response()->json([
            'status' => $isLiked->exists()
        ]);
    }


}
