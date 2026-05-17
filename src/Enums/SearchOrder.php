<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Enums;

/**
 * Sort options supported by Deezer's search endpoints. Documented at
 * https://developers.deezer.com/api/search.
 */
enum SearchOrder: string
{
    case Ranking = 'RANKING';
    case TrackAsc = 'TRACK_ASC';
    case TrackDesc = 'TRACK_DESC';
    case ArtistAsc = 'ARTIST_ASC';
    case ArtistDesc = 'ARTIST_DESC';
    case AlbumAsc = 'ALBUM_ASC';
    case AlbumDesc = 'ALBUM_DESC';
    case RatingAsc = 'RATING_ASC';
    case RatingDesc = 'RATING_DESC';
    case DurationAsc = 'DURATION_ASC';
    case DurationDesc = 'DURATION_DESC';

    public static function coerce(self|string $value): self
    {
        return $value instanceof self ? $value : self::from($value);
    }
}
