<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Facades;

use BjTheCod3r\Deezer\Actions\Albums\GetAlbumAction;
use BjTheCod3r\Deezer\Actions\Artists\GetArtistAction;
use BjTheCod3r\Deezer\Actions\Artists\GetArtistTopTracksAction;
use BjTheCod3r\Deezer\Actions\Episodes\GetEpisodeAction;
use BjTheCod3r\Deezer\Actions\Genres\GetGenreAction;
use BjTheCod3r\Deezer\Actions\Playlists\GetPlaylistAction;
use BjTheCod3r\Deezer\Actions\Podcasts\GetPodcastAction;
use BjTheCod3r\Deezer\Actions\Radios\GetRadioAction;
use BjTheCod3r\Deezer\Actions\Search\SearchAlbumsAction;
use BjTheCod3r\Deezer\Actions\Search\SearchArtistsAction;
use BjTheCod3r\Deezer\Actions\Search\SearchPlaylistsAction;
use BjTheCod3r\Deezer\Actions\Search\SearchPodcastsAction;
use BjTheCod3r\Deezer\Actions\Search\SearchRadiosAction;
use BjTheCod3r\Deezer\Actions\Search\SearchTracksAction;
use BjTheCod3r\Deezer\Actions\Search\SearchUsersAction;
use BjTheCod3r\Deezer\Actions\Tracks\GetTrackAction;
use BjTheCod3r\Deezer\Actions\Users\GetUserAction;
use BjTheCod3r\Deezer\Deezer as DeezerManager;
use Illuminate\Support\Facades\Facade;

/**
 * @method static SearchTracksAction    search(string $query)
 * @method static SearchTracksAction    searchTracks(string $query)
 * @method static SearchAlbumsAction    searchAlbums(string $query)
 * @method static SearchArtistsAction   searchArtists(string $query)
 * @method static SearchPlaylistsAction searchPlaylists(string $query)
 * @method static SearchPodcastsAction  searchPodcasts(string $query)
 * @method static SearchRadiosAction    searchRadios(string $query)
 * @method static SearchUsersAction     searchUsers(string $query)
 * @method static GetTrackAction        track(int $id)
 * @method static GetAlbumAction        album(int $id)
 * @method static GetArtistAction          artist(int $id)
 * @method static GetArtistTopTracksAction artistTopTracks(int $id)
 * @method static GetPlaylistAction     playlist(int $id)
 * @method static GetPodcastAction      podcast(int $id)
 * @method static GetRadioAction        radio(int $id)
 * @method static GetUserAction         user(int $id)
 * @method static GetGenreAction        genre(int $id)
 * @method static GetEpisodeAction      episode(int $id)
 *
 * @see DeezerManager
 */
class Deezer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return DeezerManager::class;
    }
}
