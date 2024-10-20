<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\SearchMovies;
use OpenApi\Attributes\Tag;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class SearchMoviesController
{
    public function __construct(
        private readonly SearchMovies $searchMovies,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route(path: '/api/movies/search/{query}', name: 'search_movies', options: ['expose' => true], methods: 'GET')]
    #[Tag('Movies')]
    public function __invoke(string $query): JsonResponse
    {
        try {
            $movies = $this->searchMovies->__invoke($query);

            return new JsonResponse($movies->jsonSerialize());
        } catch (\Throwable $error) {
            $this->logger->critical($error);

            return new JsonResponse(['error' => $error->getMessage()], 500);
        }
    }
}
