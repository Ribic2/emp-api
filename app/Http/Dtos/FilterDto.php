<?php

namespace App\Http\Dtos;

use Spatie\LaravelData\Data;

class FilterDto extends Data
{
    public function __construct(
        public ?string $q,
        public ?array $genres,
        public ?float $rating
    )
    {}
}
