<?php

namespace App\Http\Controllers;

use App\Http\Dtos\FilterDto;
use App\Http\Resources\MovieResource;
use App\Http\Services\MovieService;
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
        return $this->toggleAction($id, 'like');
    }

    public function favourite(int $id): JsonResponse
    {
        return $this->toggleAction($id, 'favourite');
    }

    /**
     * @param $movieId
     * @param $actionType
     * @return JsonResponse
     */
    private function toggleAction($movieId, $actionType): JsonResponse
    {
        $userId = Auth::id();

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $movie = Movie::find($movieId);
        if (!$movie) {
            return response()->json(['message' => 'Movie not found'], 404);
        }

        $actionEnum = $actionType === 'like' ? 1 : 2;

        $action = LikeAndFavourite::where('user_id', $userId)
            ->where('movie_id', $movieId)
            ->where('action_type', $actionEnum)
            ->first();

        if ($action) {
            $action->delete();
            return response()->json($action);
        } else {
            $likeAndFavourite = LikeAndFavourite::create([
                'user_id' => $userId,
                'movie_id' => $movieId,
                'action_type' => $actionEnum,
            ]);
            return response()->json($likeAndFavourite);
        }
    }
}
