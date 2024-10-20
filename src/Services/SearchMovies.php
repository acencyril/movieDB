<?php

declare(strict_types=1);

namespace App\Services;

use App\Client\TheMovieDbClient;
use App\Entities\Movies;

final class SearchMovies
{
    public function __construct(private readonly TheMovieDbClient $theMovieDbClient)
    {
    }

    public function __invoke(string $query): Movies
    {
        $movies = $this->theMovieDbClient->searchMovies($query);

        $movies = new Movies($movies['results']);

        return $this->theMovieDbClient->getTrailer($movies);
    }
}
