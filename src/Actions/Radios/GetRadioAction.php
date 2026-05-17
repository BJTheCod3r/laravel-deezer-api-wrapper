<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Radios;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\Radio;

/**
 * @method Radio get()
 * @method Radio execute()
 */
class GetRadioAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/radio/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): Radio
    {
        return Radio::fromArray($payload);
    }
}
