<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Playlists;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Playlist;

/**
 * @method Playlist get()
 * @method Playlist execute()
 */
class GetPlaylistAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/playlist/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): Playlist
    {
        return Playlist::fromArray($payload);
    }
}
