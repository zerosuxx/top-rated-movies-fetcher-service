<?php

declare(strict_types=1);

namespace AppTest\Unit\Repositories;

use App\Models\Director;
use App\Models\Genre;
use App\Models\Movie;
use App\Repositories\TopRatedMovies;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class TopRatedMoviesTest extends TestCase
{
    private MockObject $movieModelMock;
    private MockObject $genreModelMock;
    private MockObject $directorModelMock;
    private TopRatedMovies $topRatedMoviesRepository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->movieModelMock = $this->createMock(Movie::class);
        $this->genreModelMock = $this->createMock(Genre::class);
        $this->directorModelMock = $this->createMock(Director::class);
        $this->topRatedMoviesRepository = new TopRatedMovies(
            $this->movieModelMock,
            $this->genreModelMock,
            $this->directorModelMock
        );
    }

    /**
     * @test
     */
    public function save_Perfect_UpdateMovieWithGivenDetails(): void
    {
        $movieDetails = [
            'tmdb_id' => 2831934
        ];

        $movieMock = $this->createMock(Movie::class);
        $movieMock
            ->expects($this->once())
            ->method('update')
            ->with($movieDetails);

        $queryBuilderMock = $this->createMock(Builder::class);
        $queryBuilderMock
            ->expects($this->once())
            ->method('firstOrCreate')
            ->with(['tmdb_id' => $movieDetails['tmdb_id']], $movieDetails)
            ->willReturn($movieMock);

        $this->movieModelMock
            ->expects($this->once())
            ->method('newQuery')
            ->willReturn($queryBuilderMock);

        $this->topRatedMoviesRepository->save($movieDetails, [], []);
    }

    /**
     * @test
     */
    public function save_Perfect_DetachExistingGenresAndDirectors(): void
    {
        $movieDetails = [
            'tmdb_id' => 2831934
        ];

        $movieMock = $this->getMovieMock();
        $genreMock = $this->createMock(Genre::class);
        $directorMock = $this->createMock(Director::class);
        $movieMock
            ->expects($this->exactly(2))
            ->method('getAttribute')
            ->withConsecutive(
                ['genres'],
                ['directors'],
            )
            ->willReturnOnConsecutiveCalls(
                [$genreMock],
                [$directorMock]
            );

        $genresMock = $this->createMock(BelongsToMany::class);
        $genresMock
            ->expects($this->once())
            ->method('detach')
            ->with([$genreMock]);

        $movieMock
            ->expects($this->once())
            ->method('genres')
            ->willReturn($genresMock);

        $directorsMock = $this->createMock(BelongsToMany::class);
        $directorsMock
            ->expects($this->once())
            ->method('detach')
            ->with([$directorMock]);

        $movieMock
            ->expects($this->once())
            ->method('directors')
            ->willReturn($directorsMock);

        $this->topRatedMoviesRepository->save($movieDetails, [], []);
    }

    /**
     * @test
     */
    public function save_Perfect_AttachGenresAndDirectors(): void
    {
        $movieDetails = [
            'tmdb_id' => 2831934
        ];
        $genres = [
            ['id' => 1213, 'tmdb_id' => 1345123],
            ['id' => 8372, 'tmdb_id' => 72392834]
        ];
        $directors = [
            ['id' => 52131, 'tmdb_id' => 815245],
            ['id' => 937213, 'tmdb_id' => 9426172]
        ];

        $genresMock = $this->mockRelations($this->genreModelMock, Genre::class, $genres);
        $directorsMock = $this->mockRelations($this->directorModelMock, Director::class, $directors);

        $movieMock = $this->getMovieMock();
        $movieMock
            ->expects($this->exactly(3))
            ->method('genres')
            ->willReturn($genresMock);

        $movieMock
            ->expects($this->exactly(3))
            ->method('directors')
            ->willReturn($directorsMock);

        $this->topRatedMoviesRepository->save($movieDetails, $genres, $directors);
    }

    private function getMovieMock(): MockObject
    {
        $movieMock = $this->createMock(Movie::class);
        $queryBuilderMock = $this->createMock(Builder::class);
        $queryBuilderMock
            ->expects($this->once())
            ->method('firstOrCreate')
            ->willReturn($movieMock);

        $this->movieModelMock
            ->expects($this->once())
            ->method('newQuery')
            ->willReturn($queryBuilderMock);

        return $movieMock;
    }

    private function mockRelations(MockObject $modelMock, string $modelClass, array $rows): MockObject
    {
        $model1Mock = $this->createMock($modelClass);
        $model1Mock->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($rows[0]['id']);

        $model2Mock = $this->createMock($modelClass);
        $model2Mock->expects($this->once())
            ->method('getAttribute')
            ->with('id')
            ->willReturn($rows[1]['id']);

        $queryBuilderMock = $this->createMock(Builder::class);
        $queryBuilderMock
            ->expects($this->exactly(2))
            ->method('firstOrCreate')
            ->withConsecutive(
                [['tmdb_id' => $rows[0]['tmdb_id']], $rows[0]],
                [['tmdb_id' => $rows[1]['tmdb_id']], $rows[1]],
            )
            ->willReturnOnConsecutiveCalls(
                $model1Mock,
                $model2Mock
            );

        $modelMock
            ->expects($this->exactly(2))
            ->method('newQuery')
            ->willReturn($queryBuilderMock);

        $relationMock = $this->createMock(BelongsToMany::class);
        $relationMock
            ->expects($this->exactly(2))
            ->method('attach')
            ->withConsecutive([$rows[0]['id']], [$rows[1]['id']]);

        return $relationMock;
    }
}
