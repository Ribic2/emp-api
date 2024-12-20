<?php

namespace App\Http\Dtos;

use Spatie\LaravelData\Attributes\Computed;
use Spatie\LaravelData\Data;

class CSVMovieDto extends Data
{
    /**
     * @param string $id
     * @param string $title
     * @param string $published_year
     * @param array $genres
     * @param string $length_minutes
     * @param string $director
     * @param array $writers
     * @param string $production_company
     * @param array $actors
     * @param string $description
     * @param string $rating
     * @param string $number_of_votes
     */
    public function __construct(
        public string $id,
        public string $title,
        public string $published_year,
        public array  $genres,
        public string $length_minutes,
        public string $director,
        public array  $writers,
        public string $production_company,
        public array  $actors,
        public string $description,
        public string $rating,
        public string $number_of_votes
    )
    {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data[0],
            $data[1],
            $data[2],
            array_map('trim', explode(',', $data[3])),
            $data[4],
            $data[5],
            array_map('trim',explode(',', $data[6])),
            $data[7],
            array_map('trim', explode(',', $data[8])),
            $data[9],
            $data[10],
            $data[11],
        );
    }

}
