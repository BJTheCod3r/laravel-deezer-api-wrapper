<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer;

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
use BjTheCod3r\Deezer\Config\DeezerConfig;
use BjTheCod3r\Deezer\Http\DeezerClient;

/**
 * Entry point for the package. Returns fluent Action instances which the
 * caller configures further (->limit(), ->index(), ->order(), ...) before
 * calling ->get() / ->execute().
 *
 * The class itself is intentionally thin — actions are the unit of work,
 * and this manager exists only to give the public API a single discoverable
 * surface (and a Facade).
 */
class Deezer
{
    public function __construct(
        protected DeezerClient $client,
        protected DeezerConfig $config,
    ) {
    }

    /**
     * Alias for {@see searchTracks()} — Deezer's `/search` endpoint returns
     * tracks by default.
     */
    public function search(string $query): SearchTracksAction
    {
        return $this->searchTracks($query);
    }

    public function searchTracks(string $query): SearchTracksAction
    {
        return (new SearchTracksAction($this->client, $this->config))->q($query);
    }

    public function searchAlbums(string $query): SearchAlbumsAction
    {
        return (new SearchAlbumsAction($this->client, $this->config))->q($query);
    }

    public function searchArtists(string $query): SearchArtistsAction
    {
        return (new SearchArtistsAction($this->client, $this->config))->q($query);
    }

    public function searchPlaylists(string $query): SearchPlaylistsAction
    {
        return (new SearchPlaylistsAction($this->client, $this->config))->q($query);
    }

    public function searchPodcasts(string $query): SearchPodcastsAction
    {
        return (new SearchPodcastsAction($this->client, $this->config))->q($query);
    }

    public function searchRadios(string $query): SearchRadiosAction
    {
        return (new SearchRadiosAction($this->client, $this->config))->q($query);
    }

    public function searchUsers(string $query): SearchUsersAction
    {
        return (new SearchUsersAction($this->client, $this->config))->q($query);
    }

    public function track(int $id): GetTrackAction
    {
        return (new GetTrackAction($this->client))->id($id);
    }

    public function album(int $id): GetAlbumAction
    {
        return (new GetAlbumAction($this->client))->id($id);
    }

    public function artist(int $id): GetArtistAction
    {
        return (new GetArtistAction($this->client))->id($id);
    }

    public function artistTopTracks(int $id): GetArtistTopTracksAction
    {
        return (new GetArtistTopTracksAction($this->client, $this->config))->id($id);
    }

    public function playlist(int $id): GetPlaylistAction
    {
        return (new GetPlaylistAction($this->client))->id($id);
    }

    public function podcast(int $id): GetPodcastAction
    {
        return (new GetPodcastAction($this->client))->id($id);
    }

    public function radio(int $id): GetRadioAction
    {
        return (new GetRadioAction($this->client))->id($id);
    }

    public function user(int $id): GetUserAction
    {
        return (new GetUserAction($this->client))->id($id);
    }

    public function genre(int $id): GetGenreAction
    {
        return (new GetGenreAction($this->client))->id($id);
    }

    public function episode(int $id): GetEpisodeAction
    {
        return (new GetEpisodeAction($this->client))->id($id);
    }
}
