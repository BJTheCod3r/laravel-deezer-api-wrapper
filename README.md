<p align="center"><img src="art/logo.svg" alt="Laravel Deezer API Wrapper" width="380"></p>

# Laravel Deezer API Wrapper

A small, fluent Laravel wrapper around [Deezer's public API](https://developers.deezer.com/api) — currently covers **search** and **get-by-id** for tracks, albums, artists, playlists, podcasts, radios, users, genres and episodes.

Deezer's search and get endpoints are unauthenticated, so this package has zero credential setup — install it and start querying.

## Installation

```bash
composer require bjthecod3r/laravel-deezer-api-wrapper
```

The service provider and `Deezer` facade alias auto-register via Laravel's package discovery.

Publish the (optional) config file:

```bash
php artisan vendor:publish --tag=deezer-config
```

## Quick start

```php
use BjTheCod3r\Deezer\Facades\Deezer;

// Search tracks (Deezer's /search endpoint is tracks by default)
$tracks = Deezer::search('daft punk')->get();

foreach ($tracks->data as $track) {
    echo $track->title.' — '.$track->artist->name.PHP_EOL;
}

// Get a track by id
$track = Deezer::track(3135556)->get();
$track->title;          // "Harder, Better, Faster, Stronger"
$track->artist->name;   // "Daft Punk"
$track->album->title;   // "Discovery"
```

## Search

Each typed search method returns a `Paginated` of typed resources:

```php
use BjTheCod3r\Deezer\Enums\SearchOrder;
use BjTheCod3r\Deezer\Facades\Deezer;

Deezer::searchTracks('discovery')->get();      // Paginated<Track>
Deezer::searchAlbums('discovery')->get();      // Paginated<Album>
Deezer::searchArtists('daft punk')->get();     // Paginated<Artist>
Deezer::searchPlaylists('focus')->get();       // Paginated<Playlist>
Deezer::searchPodcasts('news')->get();         // Paginated<Podcast>
Deezer::searchRadios('jazz')->get();           // Paginated<Radio>
Deezer::searchUsers('someone')->get();         // Paginated<User>
```

### Fluent options

```php
Deezer::searchAlbums('discovery')
    ->limit(50)                          // results per page
    ->index(50)                          // offset (Deezer's `index`)
    ->strict()                           // disable fuzzy matching
    ->order(SearchOrder::RatingDesc)     // sort
    ->get();
```

Strict mode pairs well with Deezer's [advanced query filters](https://developers.deezer.com/api/search):

```php
Deezer::searchTracks('artist:"daft punk" album:"discovery"')
    ->strict()
    ->get();
```

### Paginated results

`Paginated::$data` is a Laravel `Collection`, so the full collection API is yours:

```php
$tracks = Deezer::search('daft punk')->limit(50)->get();

$topNames = $tracks->data
    ->filter(fn ($t) => $t->rank >= 500_000)
    ->sortByDesc('rank')
    ->pluck('title')
    ->all();

$tracks->total;   // total results available on the server
$tracks->next;    // URL for the next page, or null
$tracks->prev;    // URL for the previous page, or null
```

## Get by id

```php
Deezer::track(3135556)->get();        // Track
Deezer::album(302127)->get();         // Album (with embedded ->tracks collection)
Deezer::artist(27)->get();            // Artist
Deezer::playlist(908622995)->get();   // Playlist (with embedded ->tracks collection)
Deezer::podcast(1)->get();            // Podcast
Deezer::radio(2)->get();              // Radio
Deezer::user(3)->get();               // User
Deezer::genre(0)->get();              // Genre
Deezer::episode(5)->get();            // Episode (with ->podcast)
```

## Configuration

`config/deezer.php`:

```php
return [
    'endpoints' => [
        'api' => env('DEEZER_API_URL', 'https://api.deezer.com'),
    ],
    'defaults' => [
        'limit' => 25,
        'index' => 0,
    ],
    'http' => [
        'timeout' => env('DEEZER_HTTP_TIMEOUT', 10),
        'retry' => [
            'times' => env('DEEZER_HTTP_RETRY_TIMES', 1),
            'sleep' => env('DEEZER_HTTP_RETRY_SLEEP', 200),
        ],
    ],
];
```

## Exceptions

All exceptions extend `BjTheCod3r\Deezer\Exceptions\DeezerException`:

| Exception | When it fires |
| --- | --- |
| `ValidationException` | Missing query, missing path param, or Deezer's `ParameterException` (in-band 501) |
| `QuotaException`      | Deezer's `QuotaException` (50 req / 5s per IP) |
| `ApiException`        | Everything else — transport-level 4xx/5xx or other in-band errors |

Deezer is unusual in that it often returns **200 OK with an `{ "error": ... }` body** for application-level errors. The HTTP client detects and translates those into the same exception hierarchy as transport-level failures, so you only catch one set of types.

```php
use BjTheCod3r\Deezer\Exceptions\DeezerException;

try {
    $track = Deezer::track(0)->get();
} catch (DeezerException $e) {
    report($e);
}
```

## Extending

Each endpoint is an `Action`. To add one, extend `BjTheCod3r\Deezer\Actions\BaseAction`:

```php
use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Track;

class GetTrackContributorsAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;
        return $this;
    }

    protected function path(): string
    {
        return '/track/{id}/contributors';
    }

    protected function decode(array $payload): array
    {
        return array_map(Track::fromArray(...), $payload['data'] ?? []);
    }
}
```

## Testing

```bash
composer test
```

## License

MIT.
