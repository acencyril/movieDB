<?php

declare(strict_types=1);

namespace App\Entities;

final class Movie implements \JsonSerializable
{
    public readonly string $trailerName;
    public readonly string $trailerUrl;

    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly string $year,
        public readonly string $overview,
        public readonly float $voteAverage,
        public readonly float $voteCount,
        public readonly string $imageUrl,
        public readonly string $backdropUrl,
    ) {
    }

    public function setTrailerName(string $trailerName): self
    {
        $this->trailerName = $trailerName;

        return $this;
    }

    public function setTrailerUrl(string $trailerUrl): self
    {
        $this->trailerUrl = $trailerUrl;

        return $this;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'year' => $this->year,
            'overview' => $this->overview,
            'voteAverage' => $this->voteAverage,
            'voteCount' => $this->voteCount,
            'imageUrl' => $this->imageUrl,
            'backdropUrl' => $this->backdropUrl,
            'trailerName' => $this->trailerName,
            'trailerUrl' => $this->trailerUrl,
        ];
    }
}
