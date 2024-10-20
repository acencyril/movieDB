<?php

declare(strict_types=1);

namespace App\Controller;

use App\Services\GetMovie;
use OpenApi\Attributes\Tag;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class GetMovieController
{
    public function __construct(
        private readonly GetMovie $getMovie,
        private readonly LoggerInterface $logger
    ) { }

    #[Route(path: '/api/movies', name:'get_movie_by_id', options: ['expose' => true], methods: 'GET')]
    #[Tag('Movies')]
    public function __invoke(int $id): JsonResponse
    {
        try {
            $movies = $this->getMovie->__invoke($id);

            return new JsonResponse($movies->jsonSerialize());
        } catch (\Throwable $error) {
            $this->logger->critical($error);

            return new JsonResponse(['error' => $error->getMessage()], 500);
        }
    }
}
