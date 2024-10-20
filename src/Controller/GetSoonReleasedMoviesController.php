<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\GetSoonReleasedMovies;
use OpenApi\Attributes\Tag;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class GetSoonReleasedMoviesController
{
    public function __construct(
        private readonly GetSoonReleasedMovies $getSoonReleasedMovies,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route(path: '/api/movies/soon-released', name:'get_soon_released_movies', options: ['expose' => true], methods: 'GET')]
    #[Tag('Movies')]
    public function __invoke(): JsonResponse
    {
        try {
            $movies = $this->getSoonReleasedMovies->__invoke();

            return new JsonResponse($movies->jsonSerialize());
        } catch (\Throwable $error) {
            $this->logger->critical($error);

            return new JsonResponse(['error' => $error->getMessage()], 500);
        }
    }
}
