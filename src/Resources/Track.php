<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class Track extends Resource
{
    /**
     * @param array<int, string> $availableCountries
     * @param Collection<int, Artist> $contributors
     */
    public function __construct(
        public readonly int $id,
        public readonly ?bool $readable,
        public readonly string $title,
        public readonly ?string $titleShort,
        public readonly ?string $titleVersion,
        public readonly ?string $isrc,
        public readonly ?string $link,
        public readonly ?string $share,
        public readonly ?int $duration,
        public readonly ?int $trackPosition,
        public readonly ?int $diskNumber,
        public readonly ?int $rank,
        public readonly ?Carbon $releaseDate,
        public readonly ?bool $explicitLyrics,
        public readonly ?int $explicitContentLyrics,
        public readonly ?int $explicitContentCover,
        public readonly ?string $preview,
        public readonly ?float $bpm,
        public readonly ?float $gain,
        public readonly array $availableCountries,
        public readonly ?string $md5Image,
        public readonly Collection $contributors,
        public readonly ?Artist $artist,
        public readonly ?Album $album,
        public readonly string $type,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $rawArtist = $data['artist'] ?? null;
        $rawAlbum = $data['album'] ?? null;
        $rawCountries = $data['available_countries'] ?? [];

        /** @var array<int, string> $countries */
        $countries = is_array($rawCountries)
            ? array_values(array_map(static fn (mixed $c): string => (string) $c, $rawCountries))
            : [];

        return new self(
            id: (int) ($data['id'] ?? 0),
            readable: self::bool($data['readable'] ?? null),
            title: (string) ($data['title'] ?? ''),
            titleShort: self::str($data['title_short'] ?? null),
            titleVersion: self::str($data['title_version'] ?? null),
            isrc: self::str($data['isrc'] ?? null),
            link: self::str($data['link'] ?? null),
            share: self::str($data['share'] ?? null),
            duration: self::int($data['duration'] ?? null),
            trackPosition: self::int($data['track_position'] ?? null),
            diskNumber: self::int($data['disk_number'] ?? null),
            rank: self::int($data['rank'] ?? null),
            releaseDate: self::date($data['release_date'] ?? null),
            explicitLyrics: self::bool($data['explicit_lyrics'] ?? null),
            explicitContentLyrics: self::int($data['explicit_content_lyrics'] ?? null),
            explicitContentCover: self::int($data['explicit_content_cover'] ?? null),
            preview: self::str($data['preview'] ?? null),
            bpm: self::float($data['bpm'] ?? null),
            gain: self::float($data['gain'] ?? null),
            availableCountries: $countries,
            md5Image: self::str($data['md5_image'] ?? null),
            contributors: Artist::collection($data['contributors'] ?? []),
            artist: is_array($rawArtist) ? Artist::fromArray($rawArtist) : null,
            album: is_array($rawAlbum) ? Album::fromArray($rawAlbum) : null,
            type: (string) ($data['type'] ?? 'track'),
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
            'readable' => $this->readable,
            'title' => $this->title,
            'title_short' => $this->titleShort,
            'title_version' => $this->titleVersion,
            'isrc' => $this->isrc,
            'link' => $this->link,
            'share' => $this->share,
            'duration' => $this->duration,
            'track_position' => $this->trackPosition,
            'disk_number' => $this->diskNumber,
            'rank' => $this->rank,
            'release_date' => $this->releaseDate?->format('Y-m-d'),
            'explicit_lyrics' => $this->explicitLyrics,
            'explicit_content_lyrics' => $this->explicitContentLyrics,
            'explicit_content_cover' => $this->explicitContentCover,
            'preview' => $this->preview,
            'bpm' => $this->bpm,
            'gain' => $this->gain,
            'available_countries' => $this->availableCountries,
            'md5_image' => $this->md5Image,
            'contributors' => $this->contributors->toArray(),
            'artist' => $this->artist?->toArray(),
            'album' => $this->album?->toArray(),
            'type' => $this->type,
        ];
    }
}
