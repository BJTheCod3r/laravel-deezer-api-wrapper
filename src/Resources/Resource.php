<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Carbon;
use JsonSerializable;

abstract class Resource implements Arrayable, JsonSerializable
{
    /**
     * @return array<string, mixed>
     */
    abstract public function toArray(): array;

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    protected static function str(mixed $value): ?string
    {
        return $value === null ? null : (string) $value;
    }

    protected static function int(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (int) $value;
    }

    protected static function float(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        return (float) $value;
    }

    protected static function bool(mixed $value): ?bool
    {
        return $value === null ? null : (bool) $value;
    }

    /**
     * @return array<int|string, mixed>
     */
    protected static function arr(mixed $value): array
    {
        return is_array($value) ? $value : [];
    }

    /**
     * Deezer often wraps embedded lists as `{ "data": [...] }`. Accept either
     * shape — bare list or `{data: [...]}` envelope — and return a plain list.
     *
     * @return array<int|string, mixed>
     */
    protected static function unwrapData(mixed $value): array
    {
        if (! is_array($value)) {
            return [];
        }

        if (isset($value['data']) && is_array($value['data'])) {
            return $value['data'];
        }

        return $value;
    }

    protected static function date(mixed $value): ?Carbon
    {
        if ($value === null) {
            return null;
        }

        $string = trim((string) $value);

        if ($string === '' || $string === '0000-00-00') {
            return null;
        }

        return Carbon::parse($string);
    }
}
