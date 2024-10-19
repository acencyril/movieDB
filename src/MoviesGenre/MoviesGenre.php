<?php

declare(strict_types=1);

namespace App\MoviesGenre;

use App\Client\TheMovieDbClient;
use App\Entities\Genres;

final class MoviesGenre
{
    public function __construct(private readonly TheMovieDbClient $theMovieDbClient)
    {
    }

    public function __invoke(): Genres
    {
        $genres = $this->theMovieDbClient->getMoviesGenre();

        return new Genres($genres['genres']);
    }
}
