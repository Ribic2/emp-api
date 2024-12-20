<?php

namespace Database\Seeders;

use App\Http\Dtos\CSVMovieDto;
use App\Models\Genre;
use App\Models\Movie;
use App\Models\ProductionCompany;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create users
        User::factory()->create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => 'test123'
        ]);


        // Movies
        $file = fopen(base_path('data/movies.csv'), 'r');
        fgetcsv($file, 0, "\t");

        while (($row = fgetcsv($file)) !== false) {
            $CSVMovie = CSVMovieDto::fromArray($row);

            // Insert or retrieve production company
            $productionCompany = ProductionCompany::firstOrCreate(
                ['name' => $CSVMovie->production_company]
            );

            // Insert movie
            $movie = Movie::create([
                'imdb_title_id' => $CSVMovie->id,
                'original_title' => $CSVMovie->title,
                'year' => (int) $CSVMovie->published_year,
                'duration' => (int) $CSVMovie->length_minutes,
                'description' => $CSVMovie->description,
                'director' => $CSVMovie->director,
                'writers' => implode(', ', $CSVMovie->writers),
                'actors' => implode(', ', $CSVMovie->actors),
                'avg_vote' => (float) $CSVMovie->rating,
                'votes' => (int) $CSVMovie->number_of_votes,
                'production_company_id' => $productionCompany->id,
            ]);

            // Insert genres and associate with movie
            foreach ($CSVMovie->genres as $genreName) {
                $genre = Genre::firstOrCreate(['name' => $genreName]);
                DB::table('movie_genre')->insert([
                    'movie_id' => $movie->id,
                    'genre_id' => $genre->id,
                ]);
            }
        }

        fclose($file);
    }

}
