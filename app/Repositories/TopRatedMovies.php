<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use Illuminate\Database\Eloquent\Model;

class TopRatedMovies
{
    private Movie $movieModel;
    private Genre $genreModel;
    private Director $directorModel;

    public function __construct(Movie $movieModel, Genre $genreModel, Director $directorModel)
    {
        $this->movieModel = $movieModel;
        $this->genreModel = $genreModel;
        $this->directorModel = $directorModel;
    }

    public function save(array $movieDetails, array $genres, array $directors): void
    {
        $movie = $this->getOrCreateMovie($movieDetails);

        $movie->update($movieDetails);
        $movie->genres()->detach($movie->getAttribute('genres'));
        $movie->directors()->detach($movie->getAttribute('directors'));

        foreach ($genres as $genreAttributes) {
            $genre = $this->getOrCreateGenre($genreAttributes);
            $movie->genres()->attach($genre->getAttribute('id'));
        }

        foreach ($directors as $directorAttributes) {
            $director = $this->getOrCreateDirector($directorAttributes);
            $movie->directors()->attach($director->getAttribute('id'));
        }
    }

    /**
     * @param array $movieDetails
     * @return Movie|Model
     */
    private function getOrCreateMovie(array $movieDetails): Movie
    {
        return $this->movieModel
            ->newQuery()
            ->firstOrCreate(['tmdb_id' => $movieDetails['tmdb_id']], $movieDetails);
    }

    /**
     * @param array $genreAttributes
     * @return Genre|Model
     */
    private function getOrCreateGenre(array $genreAttributes): Genre
    {
        return $this->genreModel
            ->newQuery()
            ->firstOrCreate(['tmdb_id' => $genreAttributes['tmdb_id']], $genreAttributes);
    }

    /**
     * @param array $directorAttributes
     * @return Director|Model
     */
    private function getOrCreateDirector(array $directorAttributes): Director
    {
        return $this->directorModel
            ->newQuery()
            ->firstOrCreate(['tmdb_id' => $directorAttributes['tmdb_id']], $directorAttributes);
    }
}
