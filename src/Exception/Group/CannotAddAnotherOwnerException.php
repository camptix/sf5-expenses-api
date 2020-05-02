<?php

declare(strict_types=1);

namespace App\Exception\Group;

use Symfony\Component\HttpFoundation\File\Exception\AccessDeniedException;

class CannotAddAnotherOwnerException extends AccessDeniedException
{
    private const MESSAGE = 'You cannot add another user as owner';

    public static function create(): self
    {
        throw new self(self::MESSAGE);
    }
}
