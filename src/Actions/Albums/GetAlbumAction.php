<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Albums;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Album;

/**
 * @method Album get()
 * @method Album execute()
 */
class GetAlbumAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/album/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): Album
    {
        return Album::fromArray($payload);
    }
}
