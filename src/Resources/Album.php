<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class Album extends Resource
{
    /**
     * @param Collection<int, Genre> $genres
     * @param Collection<int, Artist> $contributors
     * @param Collection<int, Track> $tracks
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $upc,
        public readonly ?string $link,
        public readonly ?string $share,
        public readonly ?string $cover,
        public readonly ?string $coverSmall,
        public readonly ?string $coverMedium,
        public readonly ?string $coverBig,
        public readonly ?string $coverXl,
        public readonly ?string $md5Image,
        public readonly ?int $genreId,
        public readonly Collection $genres,
        public readonly ?string $label,
        public readonly ?int $nbTracks,
        public readonly ?int $duration,
        public readonly ?int $fans,
        public readonly ?Carbon $releaseDate,
        public readonly ?string $recordType,
        public readonly ?bool $available,
        public readonly ?string $tracklist,
        public readonly ?bool $explicitLyrics,
        public readonly ?int $explicitContentLyrics,
        public readonly ?int $explicitContentCover,
        public readonly Collection $contributors,
        public readonly ?Artist $artist,
        public readonly Collection $tracks,
        public readonly string $type,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $rawArtist = $data['artist'] ?? null;
        $rawGenres = self::unwrapData($data['genres'] ?? null);
        $rawTracks = self::unwrapData($data['tracks'] ?? null);

        return new self(
            id: (int) ($data['id'] ?? 0),
            title: (string) ($data['title'] ?? ''),
            upc: self::str($data['upc'] ?? null),
            link: self::str($data['link'] ?? null),
            share: self::str($data['share'] ?? null),
            cover: self::str($data['cover'] ?? null),
            coverSmall: self::str($data['cover_small'] ?? null),
            coverMedium: self::str($data['cover_medium'] ?? null),
            coverBig: self::str($data['cover_big'] ?? null),
            coverXl: self::str($data['cover_xl'] ?? null),
            md5Image: self::str($data['md5_image'] ?? null),
            genreId: self::int($data['genre_id'] ?? null),
            genres: Genre::collection($rawGenres),
            label: self::str($data['label'] ?? null),
            nbTracks: self::int($data['nb_tracks'] ?? null),
            duration: self::int($data['duration'] ?? null),
            fans: self::int($data['fans'] ?? null),
            releaseDate: self::date($data['release_date'] ?? null),
            recordType: self::str($data['record_type'] ?? null),
            available: self::bool($data['available'] ?? null),
            tracklist: self::str($data['tracklist'] ?? null),
            explicitLyrics: self::bool($data['explicit_lyrics'] ?? null),
            explicitContentLyrics: self::int($data['explicit_content_lyrics'] ?? null),
            explicitContentCover: self::int($data['explicit_content_cover'] ?? null),
            contributors: Artist::collection($data['contributors'] ?? []),
            artist: is_array($rawArtist) ? Artist::fromArray($rawArtist) : null,
            tracks: Track::collection($rawTracks),
            type: (string) ($data['type'] ?? 'album'),
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
            'upc' => $this->upc,
            'link' => $this->link,
            'share' => $this->share,
            'cover' => $this->cover,
            'cover_small' => $this->coverSmall,
            'cover_medium' => $this->coverMedium,
            'cover_big' => $this->coverBig,
            'cover_xl' => $this->coverXl,
            'md5_image' => $this->md5Image,
            'genre_id' => $this->genreId,
            'genres' => $this->genres->toArray(),
            'label' => $this->label,
            'nb_tracks' => $this->nbTracks,
            'duration' => $this->duration,
            'fans' => $this->fans,
            'release_date' => $this->releaseDate?->format('Y-m-d'),
            'record_type' => $this->recordType,
            'available' => $this->available,
            'tracklist' => $this->tracklist,
            'explicit_lyrics' => $this->explicitLyrics,
            'explicit_content_lyrics' => $this->explicitContentLyrics,
            'explicit_content_cover' => $this->explicitContentCover,
            'contributors' => $this->contributors->toArray(),
            'artist' => $this->artist?->toArray(),
            'tracks' => $this->tracks->toArray(),
            'type' => $this->type,
        ];
    }
}
