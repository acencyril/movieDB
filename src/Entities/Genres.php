<?php

declare(strict_types=1);

namespace App\Entities;

use phpDocumentor\Reflection\Types\This;

final class Genres implements \JsonSerializable
{
    /** @var Genre[] */
    private array $genres;

    public function __construct(array $genres)
    {
        $this->genres = array_map(static function (array $genre) {
            return new Genre($genre['id'], $genre['name']);
        }, $genres);
    }

    public function jsonSerialize(): mixed
    {
        return $this->genres;
    }
}
