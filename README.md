# Top Rated Movies Fetcher Service

## Install
```shell
$ make env # fill THE_MOVIE_DATABASE_API_TOKEN env variable with your token in .env file
$ make build
$ make install
```

## Run database migrations
```shell
$ make up-db # wait for the database booting up
$ make migrate-db 
```

## Fetch and store top-rated movies
```shell
$ make fetch-top-rated-movies
```

## Run code style check + analyse + tests
```shell
$ make cat
```

## Run tests
```shell
$ make test
```
