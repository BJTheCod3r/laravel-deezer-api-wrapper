<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class Episode extends Resource
{
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?bool $available,
        public readonly ?Carbon $releaseDate,
        public readonly ?int $duration,
        public readonly ?string $link,
        public readonly ?string $share,
        public readonly ?string $picture,
        public readonly ?string $pictureSmall,
        public readonly ?string $pictureMedium,
        public readonly ?string $pictureBig,
        public readonly ?string $pictureXl,
        public readonly ?Podcast $podcast,
        public readonly string $type,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $rawPodcast = $data['podcast'] ?? null;

        return new self(
            id: (int) ($data['id'] ?? 0),
            title: (string) ($data['title'] ?? ''),
            description: self::str($data['description'] ?? null),
            available: self::bool($data['available'] ?? null),
            releaseDate: self::date($data['release_date'] ?? null),
            duration: self::int($data['duration'] ?? null),
            link: self::str($data['link'] ?? null),
            share: self::str($data['share'] ?? null),
            picture: self::str($data['picture'] ?? null),
            pictureSmall: self::str($data['picture_small'] ?? null),
            pictureMedium: self::str($data['picture_medium'] ?? null),
            pictureBig: self::str($data['picture_big'] ?? null),
            pictureXl: self::str($data['picture_xl'] ?? null),
            podcast: is_array($rawPodcast) ? Podcast::fromArray($rawPodcast) : null,
            type: (string) ($data['type'] ?? 'episode'),
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
            'title' => $this->title,
            'description' => $this->description,
            'available' => $this->available,
            'release_date' => $this->releaseDate?->format('Y-m-d H:i:s'),
            'duration' => $this->duration,
            'link' => $this->link,
            'share' => $this->share,
            'picture' => $this->picture,
            'picture_small' => $this->pictureSmall,
            'picture_medium' => $this->pictureMedium,
            'picture_big' => $this->pictureBig,
            'picture_xl' => $this->pictureXl,
            'podcast' => $this->podcast?->toArray(),
            'type' => $this->type,
        ];
    }
}
