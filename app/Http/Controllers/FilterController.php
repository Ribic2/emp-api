<?php

namespace App\Http\Controllers;

use App\Models\Genre;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FilterController extends Controller
{
    public function getFilterData(): JsonResponse
    {
        return response()->json([
            'genres' => Genre::all(),
            'ratings' => range(0, 9),
        ]);
    }
}
