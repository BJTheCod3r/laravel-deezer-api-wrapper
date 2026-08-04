<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Artists;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Actions\Concerns\HasPagination;
use BjTheCod3r\Deezer\Config\DeezerConfig;
use BjTheCod3r\Deezer\Http\DeezerClient;
use BjTheCod3r\Deezer\Resources\Paginated;
use BjTheCod3r\Deezer\Resources\Track;

/**
 * @method Paginated<Track> get()
 * @method Paginated<Track> execute()
 */
class GetArtistTopTracksAction extends BaseAction
{
    use HasPagination;

    public function __construct(DeezerClient $client, protected DeezerConfig $config)
    {
        parent::__construct($client);

        $this->limit = $config->defaultLimit;
        $this->index = $config->defaultIndex;
    }

    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/artist/{id}/top';
    }

    /**
     * @return array<string, mixed>
     */
    protected function query(): array
    {
        return [
            'limit' => $this->limit,
            'index' => $this->index,
        ];
    }

    /**
     * @param array<string, mixed> $payload
     *
     * @return Paginated<Track>
     */
    protected function decode(array $payload): Paginated
    {
        return Paginated::fromArray($payload, Track::fromArray(...));
    }
}
