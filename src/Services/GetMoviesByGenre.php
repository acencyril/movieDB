<?php

declare(strict_types=1);

namespace App\Services;

use App\Client\TheMovieDbClient;
use App\Entities\Movies;

final class GetMoviesByGenre
{
    public function __construct(private readonly TheMovieDbClient $theMovieDbClient)
    {
    }

    public function __invoke(int $genreId): Movies
    {
        $movies = $this->theMovieDbClient->getMoviesByGenre($genreId);

        $movies = new Movies($movies['results']);

        return $this->theMovieDbClient->getTrailer($movies);
    }
}
