<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Resources;

use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

final class Playlist extends Resource
{
    /**
     * @param Collection<int, Track> $tracks
     */
    public function __construct(
        public readonly int $id,
        public readonly string $title,
        public readonly ?string $description,
        public readonly ?int $duration,
        public readonly ?bool $public,
        public readonly ?bool $isLovedTrack,
        public readonly ?bool $collaborative,
        public readonly ?int $nbTracks,
        public readonly ?int $unseenTrackCount,
        public readonly ?int $fans,
        public readonly ?string $link,
        public readonly ?string $share,
        public readonly ?string $picture,
        public readonly ?string $pictureSmall,
        public readonly ?string $pictureMedium,
        public readonly ?string $pictureBig,
        public readonly ?string $pictureXl,
        public readonly ?string $checksum,
        public readonly ?string $tracklist,
        public readonly ?Carbon $creationDate,
        public readonly ?string $md5Image,
        public readonly ?string $pictureType,
        public readonly ?User $creator,
        public readonly Collection $tracks,
        public readonly string $type,
    ) {
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function fromArray(array $data): self
    {
        $rawCreator = $data['creator'] ?? $data['user'] ?? null;
        $rawTracks = self::unwrapData($data['tracks'] ?? null);

        return new self(
            id: (int) ($data['id'] ?? 0),
            title: (string) ($data['title'] ?? ''),
            description: self::str($data['description'] ?? null),
            duration: self::int($data['duration'] ?? null),
            public: self::bool($data['public'] ?? null),
            isLovedTrack: self::bool($data['is_loved_track'] ?? null),
            collaborative: self::bool($data['collaborative'] ?? null),
            nbTracks: self::int($data['nb_tracks'] ?? null),
            unseenTrackCount: self::int($data['unseen_track_count'] ?? null),
            fans: self::int($data['fans'] ?? null),
            link: self::str($data['link'] ?? null),
            share: self::str($data['share'] ?? null),
            picture: self::str($data['picture'] ?? null),
            pictureSmall: self::str($data['picture_small'] ?? null),
            pictureMedium: self::str($data['picture_medium'] ?? null),
            pictureBig: self::str($data['picture_big'] ?? null),
            pictureXl: self::str($data['picture_xl'] ?? null),
            checksum: self::str($data['checksum'] ?? null),
            tracklist: self::str($data['tracklist'] ?? null),
            creationDate: self::date($data['creation_date'] ?? null),
            md5Image: self::str($data['md5_image'] ?? null),
            pictureType: self::str($data['picture_type'] ?? null),
            creator: is_array($rawCreator) ? User::fromArray($rawCreator) : null,
            tracks: Track::collection($rawTracks),
            type: (string) ($data['type'] ?? 'playlist'),
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
            'duration' => $this->duration,
            'public' => $this->public,
            'is_loved_track' => $this->isLovedTrack,
            'collaborative' => $this->collaborative,
            'nb_tracks' => $this->nbTracks,
            'unseen_track_count' => $this->unseenTrackCount,
            'fans' => $this->fans,
            'link' => $this->link,
            'share' => $this->share,
            'picture' => $this->picture,
            'picture_small' => $this->pictureSmall,
            'picture_medium' => $this->pictureMedium,
            'picture_big' => $this->pictureBig,
            'picture_xl' => $this->pictureXl,
            'checksum' => $this->checksum,
            'tracklist' => $this->tracklist,
            'creation_date' => $this->creationDate?->format('Y-m-d H:i:s'),
            'md5_image' => $this->md5Image,
            'picture_type' => $this->pictureType,
            'creator' => $this->creator?->toArray(),
            'tracks' => $this->tracks->toArray(),
            'type' => $this->type,
        ];
    }
}
