<?php

declare(strict_types=1);

namespace App\Controller;

use App\MoviesGenre\MoviesGenre;
use OpenApi\Attributes\Tag;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

final class GenresController
{
    public function __construct(
        private readonly MoviesGenre $moviesGenre,
        private readonly LoggerInterface $logger
    ) {
    }

    #[Route(path: '/api/genres', name:'genres', options: ['expose' => true], methods: 'GET')]
    #[Tag('Genres')]
    public function __invoke(): JsonResponse
    {
        try {
            $genres = $this->moviesGenre->__invoke();

            return new JsonResponse($genres->jsonSerialize());
        } catch (\Throwable $error) {
            $this->logger->critical($error);

            return new JsonResponse(['error' => $error->getMessage()], 500);
        }
    }
}
