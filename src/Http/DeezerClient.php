<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Http;

use BjTheCod3r\Deezer\Config\DeezerConfig;
use BjTheCod3r\Deezer\Exceptions\ApiException;
use BjTheCod3r\Deezer\Exceptions\DeezerException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Http;
use Throwable;

class DeezerClient
{
    public function __construct(
        protected DeezerConfig $config,
    ) {
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return array<string, mixed>
     */
    public function get(string $path, array $query = []): array
    {
        return $this->send('GET', $path, ['query' => $this->cleanQuery($query)]);
    }

    /**
     * @param array{query?: array<string, mixed>} $options
     *
     * @return array<string, mixed>
     */
    protected function send(string $method, string $path, array $options): array
    {
        try {
            $response = $this->pendingRequest()
                ->send(strtoupper($method), $this->url($path), $options);
        } catch (ConnectionException $e) {
            throw new DeezerException(
                'Could not reach Deezer API: '.$e->getMessage(),
                previous: $e,
            );
        } catch (Throwable $e) {
            throw new DeezerException(
                'Unexpected error while calling Deezer API: '.$e->getMessage(),
                previous: $e,
            );
        }

        if ($response->failed()) {
            throw ApiException::fromResponse($response);
        }

        if ($response->status() === JsonResponse::HTTP_NO_CONTENT) {
            return [];
        }

        $payload = $this->decode($response);

        // Deezer signals errors with a 200 OK whose body contains an `error`
        // object — translate those into the same exception hierarchy as
        // transport-level failures.
        if (isset($payload['error']) && is_array($payload['error'])) {
            throw ApiException::fromPayload($payload);
        }

        return $payload;
    }

    protected function pendingRequest(): PendingRequest
    {
        return Http::baseUrl($this->config->apiUrl)
            ->timeout($this->config->httpTimeout)
            ->retry($this->config->httpRetryTimes, $this->config->httpRetrySleep, throw: false)
            ->withHeaders([
                'Accept' => 'application/json',
            ])
            ->acceptJson();
    }

    protected function url(string $path): string
    {
        return '/'.ltrim($path, '/');
    }

    /**
     * Drop null/empty values so they don't end up as `?foo=` query strings.
     *
     * @param array<string, mixed> $query
     *
     * @return array<string, mixed>
     */
    protected function cleanQuery(array $query): array
    {
        return array_filter(
            $query,
            static fn (mixed $value): bool => $value !== null && $value !== '' && $value !== [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function decode(Response $response): array
    {
        $decoded = $response->json();

        return is_array($decoded) ? $decoded : [];
    }
}
