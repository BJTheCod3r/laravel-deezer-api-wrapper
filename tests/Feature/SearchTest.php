<?php

declare(strict_types=1);

use BjTheCod3r\Deezer\Enums\SearchOrder;
use BjTheCod3r\Deezer\Exceptions\ApiException;
use BjTheCod3r\Deezer\Exceptions\QuotaException;
use BjTheCod3r\Deezer\Exceptions\ValidationException;
use BjTheCod3r\Deezer\Facades\Deezer;
use BjTheCod3r\Deezer\Resources\Album;
use BjTheCod3r\Deezer\Resources\Artist;
use BjTheCod3r\Deezer\Resources\Paginated;
use BjTheCod3r\Deezer\Resources\Playlist;
use BjTheCod3r\Deezer\Resources\Podcast;
use BjTheCod3r\Deezer\Resources\Radio;
use BjTheCod3r\Deezer\Resources\Track;
use BjTheCod3r\Deezer\Resources\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;

beforeEach(function (): void {
    Http::preventStrayRequests();
});

it('hits /search and returns a Paginated of Track', function (): void {
    Http::fake([
        'api.deezer.com/search*' => Http::response([
            'data' => [[
                'id' => 3135556,
                'title' => 'Harder, Better, Faster, Stronger',
                'duration' => 224,
                'rank' => 956338,
                'preview' => 'https://cdns-preview.dzcdn.net/x.mp3',
                'artist' => ['id' => 27, 'name' => 'Daft Punk', 'type' => 'artist'],
                'album' => ['id' => 302127, 'title' => 'Discovery', 'type' => 'album'],
                'type' => 'track',
            ]],
            'total' => 1234,
            'next' => 'https://api.deezer.com/search?q=daft+punk&index=25',
        ]),
    ]);

    $tracks = Deezer::search('daft punk')->get();

    expect($tracks)->toBeInstanceOf(Paginated::class)
        ->and($tracks->total)->toBe(1234)
        ->and($tracks->next)->toBe('https://api.deezer.com/search?q=daft+punk&index=25')
        ->and($tracks->data)->toHaveCount(1)
        ->and($tracks->data[0])->toBeInstanceOf(Track::class)
        ->and($tracks->data[0]->id)->toBe(3135556)
        ->and($tracks->data[0]->title)->toBe('Harder, Better, Faster, Stronger')
        ->and($tracks->data[0]->artist)->toBeInstanceOf(Artist::class)
        ->and($tracks->data[0]->artist->name)->toBe('Daft Punk')
        ->and($tracks->data[0]->album)->toBeInstanceOf(Album::class)
        ->and($tracks->data[0]->album->title)->toBe('Discovery');

    Http::assertSent(function ($request): bool {
        return str_starts_with($request->url(), 'https://api.deezer.com/search')
            && $request['q'] === 'daft punk'
            && (string) $request['limit'] === '25'
            && (string) $request['index'] === '0';
    });
});

it('sends limit, index, strict and order to typed search endpoints', function (): void {
    Http::fake([
        'api.deezer.com/search/album*' => Http::response(['data' => [], 'total' => 0]),
    ]);

    Deezer::searchAlbums('discovery')
        ->limit(50)
        ->index(50)
        ->strict()
        ->order(SearchOrder::RatingDesc)
        ->get();

    Http::assertSent(function ($request): bool {
        return str_starts_with($request->url(), 'https://api.deezer.com/search/album')
            && $request['q'] === 'discovery'
            && (string) $request['limit'] === '50'
            && (string) $request['index'] === '50'
            && $request['strict'] === 'on'
            && $request['order'] === 'RATING_DESC';
    });
});

it('decodes searchAlbums into Album resources', function (): void {
    Http::fake([
        'api.deezer.com/search/album*' => Http::response([
            'data' => [[
                'id' => 302127,
                'title' => 'Discovery',
                'cover_medium' => 'https://e-cdns-images.dzcdn.net/discovery.jpg',
                'nb_tracks' => 14,
                'release_date' => '2001-03-07',
                'record_type' => 'album',
                'artist' => ['id' => 27, 'name' => 'Daft Punk', 'type' => 'artist'],
                'type' => 'album',
            ]],
            'total' => 1,
        ]),
    ]);

    $albums = Deezer::searchAlbums('discovery')->get();

    expect($albums->data[0])->toBeInstanceOf(Album::class)
        ->and($albums->data[0]->title)->toBe('Discovery')
        ->and($albums->data[0]->nbTracks)->toBe(14)
        ->and($albums->data[0]->releaseDate?->format('Y-m-d'))->toBe('2001-03-07')
        ->and($albums->data[0]->artist?->name)->toBe('Daft Punk');
});

it('decodes searchArtists into Artist resources', function (): void {
    Http::fake([
        'api.deezer.com/search/artist*' => Http::response([
            'data' => [[
                'id' => 27,
                'name' => 'Daft Punk',
                'picture_medium' => 'https://e-cdns-images.dzcdn.net/dp.jpg',
                'nb_album' => 30,
                'nb_fan' => 1_234_567,
                'type' => 'artist',
            ]],
            'total' => 1,
        ]),
    ]);

    $artists = Deezer::searchArtists('daft punk')->get();

    expect($artists->data[0])->toBeInstanceOf(Artist::class)
        ->and($artists->data[0]->name)->toBe('Daft Punk')
        ->and($artists->data[0]->nbFan)->toBe(1_234_567);
});

it('decodes searchPlaylists into Playlist resources', function (): void {
    Http::fake([
        'api.deezer.com/search/playlist*' => Http::response([
            'data' => [[
                'id' => 908622995,
                'title' => 'Hits of 2001',
                'nb_tracks' => 50,
                'creator' => ['id' => 1, 'name' => 'Deezer', 'type' => 'user'],
                'type' => 'playlist',
            ]],
            'total' => 1,
        ]),
    ]);

    $playlists = Deezer::searchPlaylists('hits 2001')->get();

    expect($playlists->data[0])->toBeInstanceOf(Playlist::class)
        ->and($playlists->data[0]->title)->toBe('Hits of 2001')
        ->and($playlists->data[0]->creator?->name)->toBe('Deezer');
});

it('decodes searchPodcasts, searchRadios, searchUsers into their typed resources', function (): void {
    Http::fake([
        'api.deezer.com/search/podcast*' => Http::response([
            'data' => [['id' => 1, 'title' => 'A pod', 'type' => 'podcast']],
            'total' => 1,
        ]),
        'api.deezer.com/search/radio*' => Http::response([
            'data' => [['id' => 2, 'title' => 'A radio', 'type' => 'radio']],
            'total' => 1,
        ]),
        'api.deezer.com/search/user*' => Http::response([
            'data' => [['id' => 3, 'name' => 'someone', 'type' => 'user']],
            'total' => 1,
        ]),
    ]);

    expect(Deezer::searchPodcasts('news')->get()->data[0])->toBeInstanceOf(Podcast::class)
        ->and(Deezer::searchRadios('jazz')->get()->data[0])->toBeInstanceOf(Radio::class)
        ->and(Deezer::searchUsers('someone')->get()->data[0])->toBeInstanceOf(User::class);
});

it('exposes the full Laravel Collection API on Paginated::data', function (): void {
    Http::fake([
        'api.deezer.com/search*' => Http::response([
            'data' => [
                ['id' => 1, 'title' => 'A', 'rank' => 30, 'type' => 'track'],
                ['id' => 2, 'title' => 'B', 'rank' => 80, 'type' => 'track'],
                ['id' => 3, 'title' => 'C', 'rank' => 60, 'type' => 'track'],
            ],
            'total' => 3,
        ]),
    ]);

    $tracks = Deezer::search('any')->get();

    expect($tracks->data)->toBeInstanceOf(Collection::class);

    $popular = $tracks->data
        ->filter(fn (Track $t) => $t->rank >= 60)
        ->sortByDesc('rank')
        ->pluck('title')
        ->values()
        ->all();

    expect($popular)->toBe(['B', 'C']);
});

it('serializes typed resources back to arrays via toArray', function (): void {
    Http::fake([
        'api.deezer.com/search*' => Http::response([
            'data' => [['id' => 1, 'title' => 'Doxy', 'type' => 'track']],
            'total' => 1,
        ]),
    ]);

    $tracks = Deezer::search('Doxy')->get();
    $array = $tracks->toArray();

    expect($array)->toHaveKey('data')
        ->and($array['data'][0]['title'])->toBe('Doxy')
        ->and($array['total'])->toBe(1);
});

it('rejects searches with an empty query', function (): void {
    Deezer::search('')->get();
})->throws(ValidationException::class);

it('translates Deezer in-band errors (200 OK with error body) into ApiException', function (): void {
    Http::fake([
        'api.deezer.com/search*' => Http::response([
            'error' => [
                'type' => 'Exception',
                'message' => 'something went wrong',
                'code' => 800,
            ],
        ]),
    ]);

    try {
        Deezer::search('anything')->get();
        expect(false)->toBeTrue('expected ApiException');
    } catch (ApiException $e) {
        expect($e->getCode())->toBe(800)
            ->and($e->getMessage())->toBe('something went wrong');
    }
});

it('translates Deezer ParameterException into ValidationException', function (): void {
    Http::fake([
        'api.deezer.com/search*' => Http::response([
            'error' => [
                'type' => 'ParameterException',
                'message' => 'invalid order',
                'code' => 501,
            ],
        ]),
    ]);

    try {
        Deezer::search('foo')->get();
        expect(false)->toBeTrue('expected ValidationException');
    } catch (ValidationException $e) {
        expect($e->getCode())->toBe(501)
            ->and($e->getMessage())->toBe('invalid order');
    }
});

it('translates Deezer QuotaException into QuotaException', function (): void {
    Http::fake([
        'api.deezer.com/search*' => Http::response([
            'error' => [
                'type' => 'QuotaException',
                'message' => 'Quota limit exceeded',
                'code' => 4,
            ],
        ]),
    ]);

    try {
        Deezer::search('foo')->get();
        expect(false)->toBeTrue('expected QuotaException');
    } catch (QuotaException $e) {
        expect($e->getCode())->toBe(4)
            ->and($e->getMessage())->toBe('Quota limit exceeded');
    }
});

it('translates transport-level 5xx responses into ApiException', function (): void {
    Http::fake([
        'api.deezer.com/search*' => Http::response(
            ['error' => ['type' => 'Exception', 'message' => 'boom', 'code' => 500]],
            500,
        ),
    ]);

    try {
        Deezer::search('foo')->get();
        expect(false)->toBeTrue('expected ApiException');
    } catch (ApiException $e) {
        expect($e->getCode())->toBe(500)
            ->and($e->getMessage())->toBe('boom');
    }
});

it('does not send authentication headers', function (): void {
    Http::fake([
        'api.deezer.com/search*' => Http::response(['data' => [], 'total' => 0]),
    ]);

    Deezer::search('anything')->get();

    Http::assertSent(fn ($request): bool => ! $request->hasHeader('Authorization'));
});
