<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Genres;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Genre;

/**
 * @method Genre get()
 * @method Genre execute()
 */
class GetGenreAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/genre/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): Genre
    {
        return Genre::fromArray($payload);
    }
}
