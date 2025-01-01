<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function login(Request $request): JsonResponse
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (!auth()->guard('web')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $tokenResult = auth('web')->user()->createToken('token');

        return response()->json([
            'token' => $tokenResult->accessToken,
        ]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|max:255|unique:users,username',
            'password' => 'required|min:8|confirmed',
        ]);

        $credentials = [
            'email' => $validated['email'],
            'username' => $validated['username'],
            'password' => bcrypt($validated['password']),
        ];

        if (!auth()->guard('web')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $tokenResult = auth('web')->user()->createToken('token');

        return response()->json([
            'token' => $tokenResult->accessToken,
        ]);

    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function logout(Request $request): JsonResponse
    {
        Auth::logout();

        return response()->json()->setStatusCode(200);
    }

    /**
     * @return JsonResponse
     */

    public function me(): JsonResponse
    {
        if (!Auth::check()) {
            return response()->json(['check' => false], 401);
        }

        $user = Auth::user();

        $actions = DB::table('likes_and_favourites')
            ->join('movies', 'movies.id', '=', 'likes_and_favourites.movie_id')
            ->select(
                'likes_and_favourites.action_type',
                DB::raw('GROUP_CONCAT(movies.id ORDER BY movies.id) as movie_ids'),
                DB::raw('GROUP_CONCAT(movies.original_title ORDER BY movies.id) as movie_names')
            )
            ->where('likes_and_favourites.user_id', $user->id)
            ->groupBy('likes_and_favourites.action_type')
            ->get();

        // Process the results into a structured format
        $groupedActions = [];
        foreach ($actions as $action) {
            $movieIds = explode(',', $action->movie_ids);
            $movieNames = explode(',', $action->movie_names);

            $movies = [];
            foreach ($movieIds as $index => $movieId) {
                $movies[] = [
                    'id' => (int)$movieId,
                    'name' => $movieNames[$index],
                ];
            }

            $groupedActions[$action->action_type] = $movies;
        }

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
            'actions' => [
                'likes' => $groupedActions[1] ?? [],
                'favourites' => $groupedActions[2] ?? [],
            ],
        ]);
    }

}
