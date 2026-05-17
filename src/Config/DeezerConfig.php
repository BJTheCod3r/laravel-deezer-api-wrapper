<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Config;

final class DeezerConfig
{
    public function __construct(
        public readonly string $apiUrl,
        public readonly ?int $defaultLimit,
        public readonly ?int $defaultIndex,
        public readonly int $httpTimeout,
        public readonly int $httpRetryTimes,
        public readonly int $httpRetrySleep,
    ) {
    }

    /**
     * @param array<string, mixed> $config
     */
    public static function fromArray(array $config): self
    {
        $endpoints = isset($config['endpoints']) && is_array($config['endpoints']) ? $config['endpoints'] : [];
        $defaults = isset($config['defaults']) && is_array($config['defaults']) ? $config['defaults'] : [];
        $http = isset($config['http']) && is_array($config['http']) ? $config['http'] : [];
        $retry = isset($http['retry']) && is_array($http['retry']) ? $http['retry'] : [];

        return new self(
            apiUrl: rtrim((string) ($endpoints['api'] ?? 'https://api.deezer.com'), '/'),
            defaultLimit: self::optionalInt($defaults['limit'] ?? null),
            defaultIndex: self::optionalInt($defaults['index'] ?? null),
            httpTimeout: (int) ($http['timeout'] ?? 10),
            httpRetryTimes: (int) ($retry['times'] ?? 1),
            httpRetrySleep: (int) ($retry['sleep'] ?? 200),
        );
    }

    private static function optionalInt(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }
}
