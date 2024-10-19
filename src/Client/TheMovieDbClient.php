<?php

declare(strict_types=1);

namespace App\Client;

use Psr\Log\LoggerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

final class TheMovieDbClient
{
    PRIVATE const GENRE_URI = 'genre/movie/list';

    public function __construct(
        private readonly string $apiKey,
        private readonly string $apiPath,
        private readonly HttpClientInterface $client,
        private readonly LoggerInterface $logger
    ) { }

    public function getMoviesGenre()
    {
        try {
            $response = $this->client->request(
                'GET',
                sprintf('%s/%s', $this->apiPath, self::GENRE_URI),
                [
                    'headers' => [
                        'Accept: application/json',
                        sprintf('Authorization: Bearer %s', $this->apiKey),
                    ],
                ]
            );

            return json_decode($response->getContent(), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $exception) {
            throw new \RuntimeException($exception->getMessage());
        }
    }
}
