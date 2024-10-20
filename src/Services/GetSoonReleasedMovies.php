<?php

declare(strict_types=1);

namespace App\Services;

use App\Client\TheMovieDbClient;
use App\Entities\Movies;

final class GetSoonReleasedMovies
{
    public function __construct(private readonly TheMovieDbClient $theMovieDbClient)
    {
    }

    public function __invoke(): Movies
    {
        $movies = $this->theMovieDbClient->getSoonReleasedMovies();

        $movies = new Movies($movies['results']);

        return $this->theMovieDbClient->getTrailer($movies);
    }
}
