<?php

declare(strict_types=1);

namespace App\Exception\Group;

use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class UserDoesNotExistException extends BadRequestHttpException
{
    private const MESSAGE = 'User with ID %s does not exist';

    public static function fromUserId(string $id): self
    {
        throw new self(\sprintf(self::MESSAGE, $id));
    }
}
