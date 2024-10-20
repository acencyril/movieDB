<?php

declare(strict_types=1);

namespace App\Tests\Client;

use App\Client\TheMovieDbClient;
use App\Entities\Movies;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use PHPUnit\Framework\TestCase;

final class TheMovieDbClientTest extends TestCase
{
    private TheMovieDbClient $client;
    private HttpClientInterface $httpClient;

    protected function setUp(): void
    {
        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->client = new TheMovieDbClient('dummyApiKey', 'http://api.themoviedb.org/3', 'http://trailers.themoviedb.org/', $this->httpClient);
    }

    public function testGetGenre(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->with('GET', 'http://api.themoviedb.org/3/genre/movie/list', self::anything())
            ->willReturn($this->createResponseMock(['genres' => [['id' => 28, 'name' => 'Action']]]));

        // Act
        $result = $this->client->getGenre();

        // Assert
        self::assertIsArray($result);
        self::assertArrayHasKey('genres', $result);
        self::assertCount(1, $result['genres']);
        self::assertEquals('Action', $result['genres'][0]['name']);
    }

    public function testGetGenreThrowsExceptionOnError(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->willThrowException(new \RuntimeException('Erreur lors de l\'appel à l\'API'));

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur lors de l\'appel à l\'API');

        // Act
        $this->client->getGenre();
    }

    public function testGetMoviesByGenre(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->with('GET', 'http://api.themoviedb.org/3/discover/movie?include_adult=false&include_video=true&language=fr-FR&page=1&sort_by=popularity.desc&with_genres=28', self::anything())
            ->willReturn($this->createResponseMock(['results' => [['id' => 1, 'title' => 'Movie Title']]]));

        // Act
        $result = $this->client->getMoviesByGenre(28);

        // Assert
        self::assertIsArray($result);
        self::assertArrayHasKey('results', $result);
        self::assertCount(1, $result['results']);
        self::assertEquals('Movie Title', $result['results'][0]['title']);
    }

    public function testGetMoviesByGenreThrowsExceptionOnError(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->willThrowException(new \RuntimeException('Erreur lors de l\'appel à l\'API'));

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur lors de l\'appel à l\'API');

        // Act
        $this->client->getMoviesByGenre(28);
    }

    public function testGetSoonReleasedMovies(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->with('GET', 'http://api.themoviedb.org/3/movie/upcoming', self::anything())
            ->willReturn($this->createResponseMock(['results' => [['id' => 1, 'title' => 'Upcoming Movie']]]));

        // Act
        $result = $this->client->getSoonReleasedMovies();

        // Assert
        self::assertIsArray($result);
        self::assertArrayHasKey('results', $result);
        self::assertCount(1, $result['results']);
        self::assertEquals('Upcoming Movie', $result['results'][0]['title']);
    }

    public function testGetSoonReleasedMoviesThrowsExceptionOnError(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->willThrowException(new \RuntimeException('Erreur lors de l\'appel à l\'API'));

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur lors de l\'appel à l\'API');

        // Act
        $this->client->getSoonReleasedMovies();
    }

    public function testGetMovie(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->with('GET', 'http://api.themoviedb.org/3/movie/1', self::anything())
            ->willReturn($this->createResponseMock(['id' => 1, 'title' => 'Movie Title']));

        // Act
        $result = $this->client->getMovie(1);

        // Assert
        self::assertIsArray($result);
        self::assertEquals('Movie Title', $result['title']);
    }

    public function testGetMovieThrowsExceptionOnError(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->willThrowException(new \RuntimeException('Erreur lors de l\'appel à l\'API'));

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur lors de l\'appel à l\'API');

        // Act
        $this->client->getMovie(1);
    }

    public function testGetTrailer(): void
    {
        // Arrange
        $movieData = [
            'id' => 1,
            'original_title' => 'Movie Title',
            'release_date' => '2024',
            'overview' => 'Overview',
            'vote_average' => 8.5,
            'vote_count' => 1000,
            'poster_path' => '/imageUrl',
            'backdrop_path' => '/backdropUrl',
        ];

        $movies = new Movies([$movieData]);

        $trailerData = [
            'results' => [
                ['name' => 'Official Trailer', 'key' => 'trailerKey'],
            ],
        ];

        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->with(
                'GET',
                sprintf('%s/movie/%d/videos?language=fr-FR', 'http://api.themoviedb.org/3', $movieData['id']),
                self::anything()
            )
            ->willReturn($this->createResponseMock($trailerData));

        // Act
        $result = $this->client->getTrailer($movies);

        // Assert
        $movie = $movies->movie[0];
        self::assertSame('Official Trailer', $movie->trailerName);
        self::assertSame('http://trailers.themoviedb.org/video/play?key=trailerKey', $movie->trailerUrl);
        self::assertSame($movies, $result);
    }

    public function testGetTrailerThrowsExceptionOnError(): void
    {
        // Arrange
        $movieData = [
            'id' => 1,
            'original_title' => 'Movie Title',
            'release_date' => '2024',
            'overview' => 'Overview',
            'vote_average' => 8.5,
            'vote_count' => 1000,
            'poster_path' => '/imageUrl',
            'backdrop_path' => '/backdropUrl',
        ];

        $movies = new Movies([$movieData]);

        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->willThrowException(new \RuntimeException('Erreur lors de l\'appel à l\'API'));

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur lors de l\'appel à l\'API');

        // Act
        $this->client->getTrailer($movies);
    }

    public function testSearchMovies(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->with('GET', 'http://api.themoviedb.org/3/search/movie?query=test&include_adult=false&language=fr-FR&page=1', self::anything())
            ->willReturn($this->createResponseMock(['results' => [['id' => 1, 'title' => 'Search Result']]]));

        // Act
        $result = $this->client->searchMovies('test');

        // Assert
        self::assertIsArray($result);
        self::assertArrayHasKey('results', $result);
        self::assertCount(1, $result['results']);
        self::assertEquals('Search Result', $result['results'][0]['title']);
    }

    public function testSearchMoviesThrowsExceptionOnError(): void
    {
        // Arrange
        $this->httpClient
            ->expects(self::exactly(1))
            ->method('request')
            ->willThrowException(new \RuntimeException('Erreur lors de l\'appel à l\'API'));

        // Assert
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Erreur lors de l\'appel à l\'API');

        // Act
        $this->client->searchMovies('test');
    }

    private function createResponseMock(array $data): ResponseInterface
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getContent')->willReturn(json_encode($data));
        return $response;
    }
}
