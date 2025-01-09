<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MovieResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'originalTitle' => $this->original_title,
            'isLiked' => $this->isLiked ?? false,
            'year' => $this->year,
            'duration' => $this->duration,
            'description' => $this->description,
            'director' => $this->director,
            'writers' => array_map('trim', explode(',', $this->writers)),
            'actors' => array_map('trim', explode(',', $this->actors)),
            'avg_vote' => $this->avg_vote,
            'votes' => $this->votes,
            'comments' => CommentResource::collection($this->comments ?? []),
            'production_company' => $this->production_company ?? null,
            'genres_list' => array_map('trim', explode(',', $this->genres_list))
        ];
    }
}
