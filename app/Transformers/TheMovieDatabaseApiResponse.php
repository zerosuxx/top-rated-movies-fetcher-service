<?php

declare(strict_types=1);

namespace App\Transformers;

class TheMovieDatabaseApiResponse
{
    public function transformMovieDetailsResponse(array $response): array
    {
        $result = [];
        $result['title'] = $response['title'];
        $result['length'] = $response['runtime'];
        $result['release_date'] = $response['release_date'];
        $result['overview'] = $response['overview'];
        $result['poster_url'] = "https://image.tmdb.org/t/p/w500{$response['poster_path']}";
        $result['tmdb_id'] = $response['id'];
        $result['tmdb_vote_average'] = $response['vote_average'];
        $result['tmdb_vote_count'] = $response['vote_count'];
        $result['tmdb_url'] = "https://www.themoviedb.org/movie/{$response['id']}";

        return $result;
    }

    public function transformGenreResults(array $genres): array
    {
        return array_map(static function ($genre) {
            return [
                'tmdb_id' => $genre['id'],
                'name' => $genre['name']
            ];
        }, $genres);
    }

    public function transformDirectorResults(array $directors): array
    {
        return array_map(static function ($director) {
            return [
                'tmdb_id' => $director['id'],
                'name' => $director['name'],
                'biography' => $director['biography'],
                'date_of_birth' => $director['birthday'],
            ];
        }, $directors);
    }
}
