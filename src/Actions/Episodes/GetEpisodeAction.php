<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Episodes;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Episode;

/**
 * @method Episode get()
 * @method Episode execute()
 */
class GetEpisodeAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/episode/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): Episode
    {
        return Episode::fromArray($payload);
    }
}
