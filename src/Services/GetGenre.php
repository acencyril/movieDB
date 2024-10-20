<?php

declare(strict_types=1);

namespace App\Services;

use App\Client\TheMovieDbClient;
use App\Entities\Genres;

final class GetGenre
{
    public function __construct(private readonly TheMovieDbClient $theMovieDbClient)
    {
    }

    public function __invoke(): Genres
    {
        $genres = $this->theMovieDbClient->getGenre();

        return new Genres($genres['genres']);
    }
}
