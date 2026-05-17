<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Tracks;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Track;

/**
 * @method Track get()
 * @method Track execute()
 */
class GetTrackAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/track/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): Track
    {
        return Track::fromArray($payload);
    }
}
