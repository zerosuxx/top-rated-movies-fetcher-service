<?php

declare(strict_types=1);

namespace AppTest\Unit\Console;

use App\Client\TheMovieDatabaseApiClient;
use App\Console\Commands\FetchTopRatedMovies;
use App\Repositories\TopRatedMovies;
use App\Transformers\TheMovieDatabaseApiResponse;
use GuzzleHttp\Exception\GuzzleException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class FetchTopRatedMoviesTest extends TestCase
{
    private const MAX_RESULTS = 2;
    private const STAR_WARS_MOVIE_ID = 986813;
    private const STAR_TREK_MOVIE_ID = 208631;
    private const SKIPPED_MOVIE_ID = 132134;
    private const GEORGE_LUCAS_ID = 135913;

    private MockObject $theMovieDatabaseApiClient;
    private MockObject $topRatedMovies;
    private MockObject $apiResponseTransformer;
    private FetchTopRatedMovies $fetchTopRatedMovies;

    protected function setUp(): void
    {
        parent::setUp();
        $this->theMovieDatabaseApiClient = $this->createMock(TheMovieDatabaseApiClient::class);
        $this->topRatedMovies = $this->createMock(TopRatedMovies::class);
        $this->apiResponseTransformer = $this->createMock(TheMovieDatabaseApiResponse::class);
        $this->fetchTopRatedMovies = new FetchTopRatedMovies(
            $this->theMovieDatabaseApiClient,
            $this->topRatedMovies,
            $this->apiResponseTransformer,
            $this->createMock(LoggerInterface::class),
            self::MAX_RESULTS
        );
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function handle_EmptyTopRatedMoviesList_FetchMovieDetailsNotCalled(): void
    {
        $this->theMovieDatabaseApiClient
            ->expects($this->once())
            ->method('fetchTopRatedMovies')
            ->with(1)
            ->willReturn([]);

        $this->theMovieDatabaseApiClient
            ->expects($this->never())
            ->method('fetchMovieDetails');

        $this->fetchTopRatedMovies->handle();
    }

    /**
     * @test
     * @throws GuzzleException
     */
    public function handle_Perfect_Perfect(): void
    {
        $genres = [
            [
                'id' => 186319,
                'name' => 'action'
            ],
            [
                'id' => 978314,
                'name' => 'drama'
            ]
        ];

        $director = [
            'id' => self::GEORGE_LUCAS_ID,
            'name' => 'George Lucas'
        ];

        $movieDetails = [
            [
                'id' => self::STAR_WARS_MOVIE_ID,
                'title' => 'Star Wars',
                'genres' => [$genres[0]]
            ],
            [
                'id' => self::STAR_TREK_MOVIE_ID,
                'title' => 'Star Trek',
                'genres' => [$genres[1]]
            ]
        ];

        $this->mockApiClient($movieDetails, $director);

        $this->mockApiTransformer($movieDetails, $genres, $director);

        $this->mockRepository($movieDetails, $genres, $director['id']);

        $this->fetchTopRatedMovies->handle();
    }

    private function mockApiTransformer(array $movieDetails, array $genres, array $director): void
    {
        $this->apiResponseTransformer
            ->expects($this->exactly(2))
            ->method('transformMovieDetailsResponse')
            ->withConsecutive(
                [$movieDetails[0]],
                [$movieDetails[1]]
            )
            ->willReturnOnConsecutiveCalls(
                ['tmdb_id' => $movieDetails[0]['id']],
                ['tmdb_id' => $movieDetails[1]['id']]
            );

        $this->apiResponseTransformer
            ->expects($this->exactly(2))
            ->method('transformGenreResults')
            ->withConsecutive(
                [[$genres[0]]],
                [[$genres[1]]]
            )
            ->willReturnOnConsecutiveCalls(
                [['tmdb_id' => $genres[0]['id']]],
                [['tmdb_id' => $genres[1]['id']]]
            );

        $this->apiResponseTransformer
            ->expects($this->exactly(2))
            ->method('transformDirectorResults')
            ->withConsecutive(
                [[$director]],
                [[]]
            )
            ->willReturnOnConsecutiveCalls(
                [['tmdb_id' => $director['id']]],
                []
            );
    }

    private function mockApiClient(array $movieDetails, array $director): void
    {
        $this->theMovieDatabaseApiClient
            ->expects($this->exactly(2))
            ->method('fetchTopRatedMovies')
            ->withConsecutive(
                [1],
                [2]
            )
            ->willReturnOnConsecutiveCalls(
                [
                    [
                        'id' => self::STAR_WARS_MOVIE_ID,
                    ],
                    [
                        'id' => self::STAR_TREK_MOVIE_ID,
                    ]
                ],
                [
                    [
                        'id' => self::SKIPPED_MOVIE_ID,
                    ]
                ]
            );

        $this->theMovieDatabaseApiClient
            ->expects($this->exactly(2))
            ->method('fetchMovieDetails')
            ->withConsecutive(
                [self::STAR_WARS_MOVIE_ID],
                [self::STAR_TREK_MOVIE_ID]
            )
            ->willReturnOnConsecutiveCalls(
                $movieDetails[0],
                $movieDetails[1]
            );

        $this->theMovieDatabaseApiClient
            ->expects($this->exactly(2))
            ->method('fetchMovieCredits')
            ->withConsecutive(
                [self::STAR_WARS_MOVIE_ID],
                [self::STAR_TREK_MOVIE_ID]
            )
            ->willReturnOnConsecutiveCalls(
                [
                    'crew' => [
                        [
                            'id' => self::GEORGE_LUCAS_ID,
                            'job' => 'Director',
                        ],
                        [
                            'id' => 837213,
                            'job' => 'Writer',
                        ]
                    ]
                ],
                []
            );

        $this->theMovieDatabaseApiClient
            ->expects($this->once())
            ->method('fetchPerson')
            ->with(self::GEORGE_LUCAS_ID)
            ->willReturn($director);
    }

    private function mockRepository(array $movieDetails, array $genres, $id): void
    {
        $this->topRatedMovies
            ->expects($this->exactly(2))
            ->method('save')
            ->withConsecutive(
                [
                    ['tmdb_id' => $movieDetails[0]['id']],
                    [['tmdb_id' => $genres[0]['id']]],
                    [['tmdb_id' => $id]]
                ],
                [
                    ['tmdb_id' => $movieDetails[1]['id']],
                    [['tmdb_id' => $genres[1]['id']]],
                    []
                ]
            );
    }
}
