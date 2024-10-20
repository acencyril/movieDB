<?php

declare(strict_types=1);

namespace App\Entities;

final class Movies implements \JsonSerializable
{
    /** @var Movie[] */
    public array $movie;

    public function __construct(array $movies)
    {
        $this->movie = array_map(static function (array $movie) {
            return new Movie(
                $movie['id'],
                $movie['original_title'],
                $movie['release_date'],
                $movie['overview'],
                round($movie['vote_average'], 1),
                $movie['vote_count'],
                sprintf('https://image.tmdb.org/t/p/w500%s', $movie['poster_path']),
                sprintf('https://image.tmdb.org/t/p/w500%s', $movie['backdrop_path'])
            );
        }, $movies);
    }

    public function jsonSerialize(): array
    {
        return $this->movie;
    }
}
