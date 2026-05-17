<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Concerns;

/**
 * Deezer's pagination uses `index` (offset from start) and `limit`. Most
 * list endpoints cap the limit at 100; the API silently clamps higher values.
 */
trait HasPagination
{
    protected ?int $limit = null;

    protected ?int $index = null;

    public function limit(?int $limit): static
    {
        $this->limit = $limit;

        return $this;
    }

    public function index(?int $index): static
    {
        $this->index = $index;

        return $this;
    }
}
