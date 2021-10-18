<?php

declare(strict_types=1);

namespace App\Providers;

use App\Client\TheMovieDatabaseApiClient;
use App\Console\Commands\FetchTopRatedMovies;
use App\Repositories\TopRatedMovies;
use App\Transformers\TheMovieDatabaseApiResponse;
use GuzzleHttp\Client;
use Illuminate\Log\Logger;
use Illuminate\Support\ServiceProvider;
use Laravel\Lumen\Application;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(TheMovieDatabaseApiClient::class, static function (Application $app) {
            return new TheMovieDatabaseApiClient($app->get(Client::class), env('THE_MOVIE_DATABASE_API_TOKEN'));
        });
        $this->app->singleton(FetchTopRatedMovies::class, static function (Application $app) {
            return new FetchTopRatedMovies(
                $app->get(TheMovieDatabaseApiClient::class),
                $app->get(TopRatedMovies::class),
                $app->get(TheMovieDatabaseApiResponse::class),
                $app->get(Logger::class),
                (int)env('MOVIES_MAX_RESULTS', 30)
            );
        });
    }
}
