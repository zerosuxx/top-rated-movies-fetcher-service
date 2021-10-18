<?php

declare(strict_types=1);

namespace AppTest\Unit\Transformers;

use App\Transformers\TheMovieDatabaseApiResponse;
use PHPUnit\Framework\TestCase;

class TheMovieDatabaseApiResponseTest extends TestCase
{
    private TheMovieDatabaseApiResponse $apiResponseTransformer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->apiResponseTransformer = new TheMovieDatabaseApiResponse();
    }

    /**
     * @test
     */
    public function transformMovieDetailsResponse_Perfect_Perfect()
    {
        $response = [
            'title' => 'Start Wars',
            'runtime' => 100,
            'release_date' => '1992-03-11',
            'overview' => 'description...',
            'poster_path' => 'poster.jpg',
            'id' => 976134,
            'vote_average' => 9.5,
            'vote_count' => 888,
        ];

        $result = $this->apiResponseTransformer->transformMovieDetailsResponse($response);
        $expectedResult = [
            'title' => $response['title'],
            'length' => $response['runtime'],
            'release_date' => $response['release_date'],
            'overview' => $response['overview'],
            'poster_url' => "https://image.tmdb.org/t/p/w500{$response['poster_path']}",
            'tmdb_id' => $response['id'],
            'tmdb_vote_average' => $response['vote_average'],
            'tmdb_vote_count' => $response['vote_count'],
            'tmdb_url' => "https://www.themoviedb.org/movie/{$response['id']}",
        ];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function transformGenreResults_Perfect_Perfect()
    {
        $genres = [
            [
                'id' => 13468,
                'name' => 'action'
            ]
        ];

        $result = $this->apiResponseTransformer->transformGenreResults($genres);
        $expectedResult = [
            [
                'tmdb_id' => $genres[0]['id'],
                'name' => $genres[0]['name']
            ]
        ];

        $this->assertEquals($expectedResult, $result);
    }

    /**
     * @test
     */
    public function transformDirectorResults_Perfect_Perfect()
    {
        $directors = [
            [
                'id' => 13468,
                'name' => 'George Lucas',
                'biography' => 'bio...',
                'birthday' => '1950-10-10'
            ]
        ];

        $result = $this->apiResponseTransformer->transformDirectorResults($directors);
        $expectedResult = [
            [
                'tmdb_id' => $directors[0]['id'],
                'name' => $directors[0]['name'],
                'biography' => $directors[0]['biography'],
                'date_of_birth' => $directors[0]['birthday'],
            ]
        ];

        $this->assertEquals($expectedResult, $result);
    }
}
