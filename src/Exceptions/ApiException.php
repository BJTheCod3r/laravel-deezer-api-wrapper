<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Exceptions;

use Illuminate\Http\Client\Response;

class ApiException extends DeezerException
{
    /**
     * Build an exception from a transport-level failure (HTTP 4xx/5xx).
     */
    public static function fromResponse(Response $response): DeezerException
    {
        $body = (array) $response->json();
        $error = is_array($body['error'] ?? null) ? $body['error'] : [];

        $message = (string) (
            $error['message']
            ?? $body['error_description']
            ?? "Deezer API request failed with status {$response->status()}."
        );

        $code = (int) ($error['code'] ?? $response->status());

        return self::dispatch((string) ($error['type'] ?? ''), $message, $code, $body);
    }

    /**
     * Deezer returns 200 OK even for in-band errors — the payload looks like
     * `{ "error": { "type": "...", "message": "...", "code": N } }`. Detect
     * and translate those.
     *
     * @param array<string, mixed> $payload
     */
    public static function fromPayload(array $payload): DeezerException
    {
        $error = is_array($payload['error'] ?? null) ? $payload['error'] : [];

        $message = (string) ($error['message'] ?? 'Deezer API returned an error.');
        $code = (int) ($error['code'] ?? 0);
        $type = (string) ($error['type'] ?? '');

        return self::dispatch($type, $message, $code, $payload);
    }

    /**
     * @param array<string, mixed> $context
     */
    protected static function dispatch(string $type, string $message, int $code, array $context): DeezerException
    {
        $class = match ($type) {
            'ParameterException', 'MissingParameterException', 'InvalidQueryException' => ValidationException::class,
            'QuotaException', 'ItemsLimitExceededException' => QuotaException::class,
            default => self::class,
        };

        return new $class($message, $code, context: $context);
    }
}
