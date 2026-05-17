<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Enums;

use BjTheCod3r\Deezer\Resources\Album;
use BjTheCod3r\Deezer\Resources\Artist;
use BjTheCod3r\Deezer\Resources\Playlist;
use BjTheCod3r\Deezer\Resources\Podcast;
use BjTheCod3r\Deezer\Resources\Radio;
use BjTheCod3r\Deezer\Resources\Resource;
use BjTheCod3r\Deezer\Resources\Track;
use BjTheCod3r\Deezer\Resources\User;

/**
 * The set of object types Deezer's typed search endpoints return. Each case
 * maps to a `/search/<value>` URL segment.
 */
enum SearchType: string
{
    case Track = 'track';
    case Album = 'album';
    case Artist = 'artist';
    case Playlist = 'playlist';
    case Podcast = 'podcast';
    case Radio = 'radio';
    case User = 'user';

    /**
     * @return callable(array<string, mixed>): Resource
     */
    public function resourceFactory(): callable
    {
        return match ($this) {
            self::Track => Track::fromArray(...),
            self::Album => Album::fromArray(...),
            self::Artist => Artist::fromArray(...),
            self::Playlist => Playlist::fromArray(...),
            self::Podcast => Podcast::fromArray(...),
            self::Radio => Radio::fromArray(...),
            self::User => User::fromArray(...),
        };
    }
}
