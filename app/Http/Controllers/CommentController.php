<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Movie;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function addComment(Request $request): JsonResponse
    {

        $validated = $request->validate([
            'comment' => 'required|string|max:1000',
            'movie_id' => 'required'
        ]);

        $movie = Movie::findOrFail($request->get('movie_id'));

        $comment = new Comment();
        $comment->comment = $validated['comment'];
        $comment->movie_id = $movie->id;
        $comment->user_id = auth()->id();
        $comment->save();


        return response()->json($comment);
    }
}
