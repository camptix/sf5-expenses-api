<?php

declare(strict_types=1);

namespace App\Api\Listener\Group;

use App\Api\Listener\PreWriteListener;
use App\Exception\Group\CannotAddAnotherOwnerException;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class GroupPreWriteListener implements PreWriteListener
{
    private const POST_GROUP = 'api_groups_post_collection';

    private TokenStorageInterface $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function onKernelView(ViewEvent $event): void
    {
        /** @var User $tokenUser */
        $tokenUser = $this->tokenStorage->getToken()->getUser();

        $request = $event->getRequest();

        if (self::POST_GROUP === $request->get('_route')) {
            /** @var Group $group */
            $group = $event->getControllerResult();
            // Lo voy a dejar asi porque lo hace el curso, pero no tiene sentido que si puedo obtener el user del token
            // lo pidamos como parámetro en la creación, ya que por eso lo tengo que enviar y ademas comprobar que coincide
            // con el token. Doble trabajo para mandarlo y para procesarlo.
            if (!$group->isOwnerBy($tokenUser)) {
                throw CannotAddAnotherOwnerException::create();
            }

            $group->addUser($tokenUser);
        }
    }
}
