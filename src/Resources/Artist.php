<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Resources;

use Illuminate\Support\Collection;

final class Artist extends Resource
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly ?string $link,
        public readonly ?string $share,
        public readonly ?string $picture,
        public readonly ?string $pictureSmall,
        public readonly ?string $pictureMedium,
        public readonly ?string $pictureBig,
        public readonly ?string $pictureXl,
        public readonly ?int $nbAlbum,
        public readonly ?int $nbFan,
        public readonly ?bool $radio,
        public readonly ?string $tracklist,
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
            link: self::str($data['link'] ?? null),
            share: self::str($data['share'] ?? null),
            picture: self::str($data['picture'] ?? null),
            pictureSmall: self::str($data['picture_small'] ?? null),
            pictureMedium: self::str($data['picture_medium'] ?? null),
            pictureBig: self::str($data['picture_big'] ?? null),
            pictureXl: self::str($data['picture_xl'] ?? null),
            nbAlbum: self::int($data['nb_album'] ?? null),
            nbFan: self::int($data['nb_fan'] ?? null),
            radio: self::bool($data['radio'] ?? null),
            tracklist: self::str($data['tracklist'] ?? null),
            type: (string) ($data['type'] ?? 'artist'),
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
            'link' => $this->link,
            'share' => $this->share,
            'picture' => $this->picture,
            'picture_small' => $this->pictureSmall,
            'picture_medium' => $this->pictureMedium,
            'picture_big' => $this->pictureBig,
            'picture_xl' => $this->pictureXl,
            'nb_album' => $this->nbAlbum,
            'nb_fan' => $this->nbFan,
            'radio' => $this->radio,
            'tracklist' => $this->tracklist,
            'type' => $this->type,
        ];
    }
}
