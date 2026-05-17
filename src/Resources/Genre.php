<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Resources;

use Illuminate\Support\Collection;

final class Genre extends Resource
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $picture,
        public readonly ?string $pictureSmall,
        public readonly ?string $pictureMedium,
        public readonly ?string $pictureBig,
        public readonly ?string $pictureXl,
        public readonly string $type,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: (int) ($data['id'] ?? 0),
            name: (string) ($data['name'] ?? ''),
            picture: self::str($data['picture'] ?? null),
            pictureSmall: self::str($data['picture_small'] ?? null),
            pictureMedium: self::str($data['picture_medium'] ?? null),
            pictureBig: self::str($data['picture_big'] ?? null),
            pictureXl: self::str($data['picture_xl'] ?? null),
            type: (string) ($data['type'] ?? 'genre'),
        );
    }

    /**
     * @return Collection<int, self>
     */
    public static function collection(mixed $data): Collection
    {
        if (! is_array($data)) {
            return collect();
        }

        return collect($data)
            ->map(static fn (mixed $item): self => self::fromArray(is_array($item) ? $item : []))
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'picture' => $this->picture,
            'picture_small' => $this->pictureSmall,
            'picture_medium' => $this->pictureMedium,
            'picture_big' => $this->pictureBig,
            'picture_xl' => $this->pictureXl,
            'type' => $this->type,
        ];
    }
}
