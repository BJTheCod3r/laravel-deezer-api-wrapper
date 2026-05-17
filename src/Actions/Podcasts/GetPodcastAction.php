<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Podcasts;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Podcast;

/**
 * @method Podcast get()
 * @method Podcast execute()
 */
class GetPodcastAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/podcast/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): Podcast
    {
        return Podcast::fromArray($payload);
    }
}
