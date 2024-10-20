<?php

declare(strict_types=1);

namespace App\Services;

use App\Client\TheMovieDbClient;
use App\Entities\Movies;

final class GetMovie
{
    public function __construct(private readonly TheMovieDbClient $theMovieDbClient)
    {
    }

    public function __invoke(int $id): Movies
    {
        $movies = $this->theMovieDbClient->getMovie($id);

        return new Movies($movies['results']);
    }
}
