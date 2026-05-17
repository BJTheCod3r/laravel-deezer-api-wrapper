<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Resources;

use Illuminate\Support\Collection;

/**
 * Deezer's standard list envelope: `{ data: [...], total: N, next: "url" }`.
 * `total` and `next`/`prev` may be absent on some endpoints (e.g. /chart) —
 * absent values surface as null / 0 here.
 *
 * @template T
 */
final class Paginated extends Resource
{
    /**
     * @param Collection<int, T> $data
     */
    public function __construct(
        public readonly Collection $data,
        public readonly int $total,
        public readonly ?string $next,
        public readonly ?string $prev,
    ) {
    }

    /**
     * @template U
     *
     * @param array<string, mixed> $payload
     * @param callable(array<string, mixed>): U $itemFactory
     *
     * @return self<U>
     */
    public static function fromArray(array $payload, callable $itemFactory): self
    {
        $rawItems = self::arr($payload['data'] ?? []);

        return new self(
            data: collect($rawItems)
                ->filter(static fn (mixed $item): bool => is_array($item))
                ->map(static fn (array $item) => $itemFactory($item))
                ->values(),
            total: (int) ($payload['total'] ?? count($rawItems)),
            next: self::str($payload['next'] ?? null),
            prev: self::str($payload['prev'] ?? null),
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'data' => $this->data->toArray(),
            'total' => $this->total,
            'next' => $this->next,
            'prev' => $this->prev,
        ];
    }
}
