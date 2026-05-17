<?php

declare(strict_types=1);

namespace BjTheCod3r\Deezer\Actions\Users;

use BjTheCod3r\Deezer\Actions\BaseAction;
use BjTheCod3r\Deezer\Resources\User;

/**
 * @method User get()
 * @method User execute()
 */
class GetUserAction extends BaseAction
{
    public function id(int $id): static
    {
        $this->pathParameters['id'] = (string) $id;

        return $this;
    }

    protected function path(): string
    {
        return '/user/{id}';
    }

    /**
     * @param array<string, mixed> $payload
     */
    protected function decode(array $payload): User
    {
        return User::fromArray($payload);
    }
}
