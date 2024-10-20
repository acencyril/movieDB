<?php

declare(strict_types=1);

namespace App\Client;

use App\Entities\Movies;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TheMovieDbClient
{
    private const GENRE_URI = 'genre/movie/list';
    private const MOVIES_BY_GENRE_URI = 'discover/movie?include_adult=false&include_video=true&language=fr-FR&page=1&sort_by=popularity.desc&with_genres=';
    private const SOON_RELEASED_URI = 'movie/upcoming';
    private const MOVIE_URI = 'movie/';
    private const TRAILER_URI = 'video/play?key=';
    private const SEARCH_MOVIES_URI = '/search/movie?query=';

    public function __construct(
        private readonly string $apiKey,
        private readonly string $apiPath,
        private readonly string $trailerPath,
        private readonly HttpClientInterface $client
    ) { }

    public function getGenre(): array
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/%s', $this->apiPath, self::GENRE_URI),
                $this->getHeaders()
            );

            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }

    public function getMoviesByGenre(int $genreId): array
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/%s%d', $this->apiPath, self::MOVIES_BY_GENRE_URI, $genreId),
                $this->getHeaders()
            );

            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }

    public function getSoonReleasedMovies(): array
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/%s', $this->apiPath, self::SOON_RELEASED_URI),
                $this->getHeaders()
            );
            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }

    public function getMovie(int $id): array
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/%s%d', $this->apiPath, self::MOVIE_URI, $id),
                $this->getHeaders()
            );

            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }

    public function getTrailer(Movies $movies): Movies
    {
        try {
            foreach ($movies->movie as $movie) {
                $response = $this->client->request(
                    'GET',
                    sprintf('%s/%s%d/videos?language=fr-FR', $this->apiPath, self::MOVIE_URI, $movie->id),
                    $this->getHeaders()
                );

                $trailer = json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);

                $trailerName = $movie->title;
                $trailerUrl = '';

                if (isset($trailer['results'][0])) {
                    $trailerName = $trailer['results'][0]['name'];
                    $trailerUrl = sprintf('%s%s%s', $this->trailerPath, self::TRAILER_URI, $trailer['results'][0]['key']);
                }

                $movie
                    ->setTrailerName($trailerName)
                    ->setTrailerUrl($trailerUrl);
            }

            return $movies;
        } catch (\Throwable $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }

    public function searchMovies(string $query): array
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s%s%s&include_adult=false&language=fr-FR&page=1', $this->apiPath, self::SEARCH_MOVIES_URI, $query),
                $this->getHeaders()
            );

            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }

    private function getHeaders(): array
    {
        return   [
            'headers' => [
                'Accept: application/json',
                sprintf('Authorization: Bearer %s', $this->apiKey),
            ],
        ];
    }
}
