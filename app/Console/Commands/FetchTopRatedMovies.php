<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Client\TheMovieDatabaseApiClient;
use App\Repositories\TopRatedMovies;
use App\Transformers\TheMovieDatabaseApiResponse;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Console\Command;
use Psr\Log\LoggerInterface;

class FetchTopRatedMovies extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:top-rated-movies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch and save N pieces of top rated movies';

    private TheMovieDatabaseApiClient $theMovieDatabaseApiClient;
    private TopRatedMovies $topRatedMoviesRepository;
    private TheMovieDatabaseApiResponse $apiResponseTransformer;
    private LoggerInterface $logger;
    private int $maxResults;

    public function __construct(
        TheMovieDatabaseApiClient   $theMovieDatabaseApiClient,
        TopRatedMovies              $topRatedMoviesRepository,
        TheMovieDatabaseApiResponse $apiResponseTransformer,
        LoggerInterface             $logger,
        int                         $maxResults
    ) {
        parent::__construct();
        $this->theMovieDatabaseApiClient = $theMovieDatabaseApiClient;
        $this->topRatedMoviesRepository = $topRatedMoviesRepository;
        $this->apiResponseTransformer = $apiResponseTransformer;
        $this->logger = $logger;
        $this->maxResults = $maxResults;
    }


    /**
     * @throws GuzzleException
     */
    public function handle(): void
    {
        $movies = $this->fetchTopRatedMovies();

        foreach ($movies as $k => $movie) {
            $movieDetails = $this->theMovieDatabaseApiClient->fetchMovieDetails($movie['id']);
            $movieCredits = $this->theMovieDatabaseApiClient->fetchMovieCredits($movie['id']);
            $directors = $this->fetchDirectors($movieCredits);

            $transformedMovieDetails = $this->apiResponseTransformer->transformMovieDetailsResponse($movieDetails);
            $transformedGenres = $this->apiResponseTransformer->transformGenreResults($movieDetails['genres']);
            $transformedDirectors = $this->apiResponseTransformer->transformDirectorResults($directors);

            $this->topRatedMoviesRepository->save($transformedMovieDetails, $transformedGenres, $transformedDirectors);

            $this->logger->info("movie successfully saved", [
                'position' => $k + 1,
                'title' => $movieDetails['title'],
                'genres_count' => count($movieDetails['genres']),
                'directors_count' => count($directors)
            ]);
        }
    }

    /**
     * @throws GuzzleException
     */
    private function fetchTopRatedMovies(): array
    {
        $movies = [];
        $page = 1;
        while (count($movies) <= $this->maxResults) {
            $topRatedMovies = $this->theMovieDatabaseApiClient->fetchTopRatedMovies($page);

            if ($topRatedMovies === []) {
                break;
            }

            $movies = array_merge($movies, $topRatedMovies);

            $this->logger->info("movies fetched successfully", [
                'page' => $page,
                'count' => count($movies),
            ]);

            $page++;
        }

        return array_slice($movies, 0, $this->maxResults);
    }

    /**
     * @throws GuzzleException
     */
    private function fetchDirectors(array $movieCredits): array
    {
        $directors = [];
        if (isset($movieCredits['crew'])) {
            $movieDirectors = array_filter($movieCredits['crew'], static function ($crew) {
                return $crew['job'] === 'Director';
            });
            $directors = array_map(function ($director) {
                return $this->theMovieDatabaseApiClient->fetchPerson($director['id']);
            }, $movieDirectors);
        }

        return $directors;
    }
}
