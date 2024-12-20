<?php

namespace App\Jobs;

use App\Models\Genre;
use App\Models\Movie;
use App\Models\Type;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Bus\Batchable;

class MovieUploadByChunks implements ShouldQueue
{
    use Queueable, Batchable;

    /**
     * Create a new job instance.
     */
    public function __construct(public $chunk)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $genreCache = Genre::all()->pluck('id', 'name');
        $typeCache = Type::all()->pluck('id', 'name');

        DB::transaction(function () use ($genreCache, $typeCache) {
            $movieGenres = [];
            $movies = $this->chunk->map(function ($row) use ($typeCache, $genreCache, &$movieGenres) {

                $genres = explode(',', $row[8]);
                foreach ($genres as $genre) {
                    if ($genreCache->has($genre)) {
                        $movieGenres[] = [
                            'movie_id' => $row[0],
                            'genre_id' => $genreCache->get($genre),
                        ];
                    }

                }


                return [
                    'tconst' => $row[0],
                    'title' => $row[2],
                    'original_title' => $row[3],
                    'type_id' => $typeCache->get($row[1]) ?? null,
                    'start_year' => $row[5] === '\\N' ? null : $row[5],
                    'runtime_minutes' => $row[7] === '\\N' ? null : $row[7],
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            })->toArray();


            DB::table('movies')->insert($movies);
            DB::table('movie_genre')->insert($movieGenres);

            /*$movieIds = Movie::latest('id')->take(count($movies))->pluck('id')->toArray();
            $movieGenres = [];

            foreach ($this->chunk as $index => $row) {
                $genres = explode(',', $row[8]);
                foreach ($genres as $genre) {
                    if (isset($movieIds[$index])) {
                        if ($genreCache->has($genre)) {
                            $movieGenres[] = [
                                'movie_id' => $movieIds[$index],
                                'genre_id' => $genreCache->get($genre),
                            ];
                        }
                    }
                }
            }*/

        });
    }
}
