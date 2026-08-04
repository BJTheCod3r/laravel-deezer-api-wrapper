<?php

declare(strict_types=1);

use BjTheCod3r\Deezer\Exceptions\ValidationException;
use BjTheCod3r\Deezer\Facades\Deezer;
use BjTheCod3r\Deezer\Resources\Album;
use BjTheCod3r\Deezer\Resources\Artist;
use BjTheCod3r\Deezer\Resources\Episode;
use BjTheCod3r\Deezer\Resources\Genre;
use BjTheCod3r\Deezer\Resources\Paginated;
use BjTheCod3r\Deezer\Resources\Playlist;
use BjTheCod3r\Deezer\Resources\Podcast;
use BjTheCod3r\Deezer\Resources\Radio;
use BjTheCod3r\Deezer\Resources\Track;
use BjTheCod3r\Deezer\Resources\User;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Http::preventStrayRequests();
});

it('gets a track by id', function (): void {
    Http::fake([
        'api.deezer.com/track/3135556' => Http::response([
            'id' => 3135556,
            'title' => 'Harder, Better, Faster, Stronger',
            'duration' => 224,
            'rank' => 956338,
            'release_date' => '2001-03-07',
            'preview' => 'https://cdns-preview.dzcdn.net/x.mp3',
            'bpm' => 123.4,
            'artist' => ['id' => 27, 'name' => 'Daft Punk', 'type' => 'artist'],
            'album' => [
                'id' => 302127,
                'title' => 'Discovery',
                'cover_medium' => 'https://e-cdns-images.dzcdn.net/discovery.jpg',
                'type' => 'album',
            ],
            'type' => 'track',
        ]),
    ]);

    $track = Deezer::track(3135556)->get();

    expect($track)->toBeInstanceOf(Track::class)
        ->and($track->id)->toBe(3135556)
        ->and($track->title)->toBe('Harder, Better, Faster, Stronger')
        ->and($track->duration)->toBe(224)
        ->and($track->bpm)->toBe(123.4)
        ->and($track->releaseDate?->format('Y-m-d'))->toBe('2001-03-07')
        ->and($track->artist?->name)->toBe('Daft Punk')
        ->and($track->album?->title)->toBe('Discovery');

    expect($track->toArray()['title'])->toBe('Harder, Better, Faster, Stronger');
});

it('gets an album by id and hydrates embedded tracks', function (): void {
    Http::fake([
        'api.deezer.com/album/302127' => Http::response([
            'id' => 302127,
            'title' => 'Discovery',
            'upc' => '724384960650',
            'nb_tracks' => 14,
            'duration' => 3700,
            'fans' => 200000,
            'release_date' => '2001-03-07',
            'record_type' => 'album',
            'available' => true,
            'explicit_lyrics' => false,
            'genres' => ['data' => [['id' => 113, 'name' => 'Dance', 'type' => 'genre']]],
            'artist' => ['id' => 27, 'name' => 'Daft Punk', 'type' => 'artist'],
            'tracks' => ['data' => [
                ['id' => 3135556, 'title' => 'Harder, Better, Faster, Stronger', 'type' => 'track'],
                ['id' => 3135557, 'title' => 'Aerodynamic', 'type' => 'track'],
            ]],
            'type' => 'album',
        ]),
    ]);

    $album = Deezer::album(302127)->get();

    expect($album)->toBeInstanceOf(Album::class)
        ->and($album->title)->toBe('Discovery')
        ->and($album->upc)->toBe('724384960650')
        ->and($album->genres)->toHaveCount(1)
        ->and($album->genres[0])->toBeInstanceOf(Genre::class)
        ->and($album->genres[0]->name)->toBe('Dance')
        ->and($album->tracks)->toHaveCount(2)
        ->and($album->tracks[0])->toBeInstanceOf(Track::class)
        ->and($album->tracks[1]->title)->toBe('Aerodynamic')
        ->and($album->artist?->name)->toBe('Daft Punk');
});

it('gets an artist by id', function (): void {
    Http::fake([
        'api.deezer.com/artist/27' => Http::response([
            'id' => 27,
            'name' => 'Daft Punk',
            'nb_album' => 30,
            'nb_fan' => 9_876_543,
            'picture_xl' => 'https://e-cdns-images.dzcdn.net/dp_xl.jpg',
            'type' => 'artist',
        ]),
    ]);

    $artist = Deezer::artist(27)->get();

    expect($artist)->toBeInstanceOf(Artist::class)
        ->and($artist->name)->toBe('Daft Punk')
        ->and($artist->nbFan)->toBe(9_876_543)
        ->and($artist->pictureXl)->toBe('https://e-cdns-images.dzcdn.net/dp_xl.jpg');
});

it('gets artist top tracks by artist id', function (): void {
    Http::fake([
        'api.deezer.com/artist/27/top?limit=2&index=5' => Http::response([
            'data' => [
                [
                    'id' => 3135556,
                    'title' => 'Harder, Better, Faster, Stronger',
                    'rank' => 956338,
                    'artist' => ['id' => 27, 'name' => 'Daft Punk', 'type' => 'artist'],
                    'type' => 'track',
                ],
                [
                    'id' => 3135553,
                    'title' => 'One More Time',
                    'rank' => 895695,
                    'artist' => ['id' => 27, 'name' => 'Daft Punk', 'type' => 'artist'],
                    'type' => 'track',
                ],
            ],
            'total' => 50,
            'next' => 'https://api.deezer.com/artist/27/top?limit=2&index=7',
        ]),
    ]);

    $tracks = Deezer::artistTopTracks(27)->limit(2)->index(5)->get();

    expect($tracks)->toBeInstanceOf(Paginated::class)
        ->and($tracks->data)->toHaveCount(2)
        ->and($tracks->data[0])->toBeInstanceOf(Track::class)
        ->and($tracks->data[0]->title)->toBe('Harder, Better, Faster, Stronger')
        ->and($tracks->data[0]->rank)->toBe(956338)
        ->and($tracks->data[0]->artist?->name)->toBe('Daft Punk')
        ->and($tracks->total)->toBe(50)
        ->and($tracks->next)->toBe('https://api.deezer.com/artist/27/top?limit=2&index=7');
});

it('gets a playlist with embedded tracks', function (): void {
    Http::fake([
        'api.deezer.com/playlist/908622995' => Http::response([
            'id' => 908622995,
            'title' => 'Hits of 2001',
            'description' => 'Bops',
            'duration' => 7200,
            'public' => true,
            'nb_tracks' => 2,
            'fans' => 1000,
            'creation_date' => '2020-01-15 12:34:56',
            'creator' => ['id' => 1, 'name' => 'Deezer', 'type' => 'user'],
            'tracks' => ['data' => [
                ['id' => 1, 'title' => 'A', 'type' => 'track'],
                ['id' => 2, 'title' => 'B', 'type' => 'track'],
            ]],
            'type' => 'playlist',
        ]),
    ]);

    $playlist = Deezer::playlist(908622995)->get();

    expect($playlist)->toBeInstanceOf(Playlist::class)
        ->and($playlist->title)->toBe('Hits of 2001')
        ->and($playlist->public)->toBeTrue()
        ->and($playlist->creationDate?->format('Y-m-d H:i:s'))->toBe('2020-01-15 12:34:56')
        ->and($playlist->creator?->name)->toBe('Deezer')
        ->and($playlist->tracks)->toHaveCount(2)
        ->and($playlist->tracks[1]->title)->toBe('B');
});

it('gets a podcast, radio, user, genre, episode by id', function (): void {
    Http::fake([
        'api.deezer.com/podcast/100' => Http::response(['id' => 100, 'title' => 'Pod', 'type' => 'podcast']),
        'api.deezer.com/radio/200' => Http::response(['id' => 200, 'title' => 'Radio', 'type' => 'radio']),
        'api.deezer.com/user/300' => Http::response(['id' => 300, 'name' => 'Bolaji', 'type' => 'user']),
        'api.deezer.com/genre/0' => Http::response(['id' => 0, 'name' => 'All', 'type' => 'genre']),
        'api.deezer.com/episode/500' => Http::response([
            'id' => 500,
            'title' => 'Ep 1',
            'release_date' => '2024-01-01 10:00:00',
            'podcast' => ['id' => 100, 'title' => 'Pod', 'type' => 'podcast'],
            'type' => 'episode',
        ]),
    ]);

    expect(Deezer::podcast(100)->get())->toBeInstanceOf(Podcast::class)
        ->and(Deezer::radio(200)->get())->toBeInstanceOf(Radio::class)
        ->and(Deezer::user(300)->get())->toBeInstanceOf(User::class)
        ->and(Deezer::genre(0)->get())->toBeInstanceOf(Genre::class);

    $episode = Deezer::episode(500)->get();
    expect($episode)->toBeInstanceOf(Episode::class)
        ->and($episode->podcast)->toBeInstanceOf(Podcast::class)
        ->and($episode->podcast->title)->toBe('Pod')
        ->and($episode->releaseDate?->format('Y-m-d H:i:s'))->toBe('2024-01-01 10:00:00');
});

it('throws ValidationException when no id is supplied', function (): void {
    Http::fake();

    (new BjTheCod3r\Deezer\Actions\Tracks\GetTrackAction(
        app(BjTheCod3r\Deezer\Http\DeezerClient::class),
    ))->get();
})->throws(ValidationException::class, 'Missing required path parameter: id.');
