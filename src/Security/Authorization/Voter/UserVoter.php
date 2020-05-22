<?php

declare(strict_types=1);

namespace App\Security\Authorization\Voter;

use App\Entity\User;
use App\Security\Role;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class UserVoter extends BaseVoter
{
    public const USER_READ = 'USER_READ';
    public const USER_UPDATE = 'USER_UPDATE';
    public const USER_DELETE = 'USER_DELETE';

    protected function supports(string $attribute, $subject): bool
    {
        return \in_array($attribute, $this->getSupportedAttributes(), true);
    }

    //Estamos especificando que $subject es del tipo User y que puede ser nulo.

    /**
     * @param User|null $subject
     */
    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {
        /** @var User $tokenUser */
        $tokenUser = $token->getUser();
        //Leer usuarios
        if (self::USER_READ === $attribute) {
            //Si quiero la lista completa (mando null) y soy admin
            if (null === $subject) {
                return $this->security->isGranted(Role::ROLE_ADMIN);
            }
            //Si soy admin o es mi usuario
            return $this->security->isGranted(Role::ROLE_ADMIN) || $subject->equals($tokenUser);
        }

        //Modificar o eliminar usuario
        if (\in_array($attribute, [self::USER_UPDATE, self::USER_DELETE])) {
            //Si soy Admin o es mi usuario
            return $this->security->isGranted(Role::ROLE_ADMIN) || $subject->equals($tokenUser);
        }

        return false;
    }

    private function getSupportedAttributes(): array
    {
        return [
            self::USER_READ,
            self::USER_UPDATE,
            self::USER_DELETE,
        ];
    }
}
