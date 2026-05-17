<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Artists;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Artist;

/**
 * @method Artist get()
 * @method Artist execute()
 */
class GetArtistAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/artist/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): Artist
    {
        return Artist::fromArray($payload);
    }
}
