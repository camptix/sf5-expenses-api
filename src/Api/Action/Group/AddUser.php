<?php

declare(strict_types=1);

namespace App\Api\Action\Group;

use App\Api\Action\RequestTransformer;
use App\Entity\User;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\ConflictHttpException;
use Symfony\Component\Routing\Annotation\Route;

class AddUser
{
    private UserRepository $userRepository;
    private GroupRepository $groupRepository;

    public function __construct(UserRepository $userRepository, GroupRepository $groupRepository)
    {
        $this->userRepository = $userRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @Route("/groups/add_user", methods={"POST"})
     *
     * @return JsonResponse
     */
    public function __invoke(Request $request, User $user): JsonResponse
    {
        //En este nivel symfony no nos permite recuperar el tokenStorage por lo que esto sería nulo
        //Para resolverlo usamos un argumentResolver
        //$user = $this->tokenStorage->getToken()->getUser();
        $groupId = RequestTransformer::getRequiredField($request, 'group_id');
        $userId = RequestTransformer::getRequiredField($request, 'user_id');

        $group = $this->groupRepository->findOneById($groupId);
        if (null === $group) {
            throw new BadRequestHttpException('Group not found');
        }

        if (!$this->groupRepository->userIsMember($group, $user)) {
            throw new BadRequestHttpException('You cannot add users to this group');
        }

        $newUser = $this->userRepository->findOneById($userId);
        if (null === $newUser) {
            throw new BadRequestHttpException('User not found');
        }

        //Si el usuario ya existe en este grupo lanzamos una excepcion
        if ($this->groupRepository->userIsMember($group, $newUser)) {
            throw new ConflictHttpException('This user is already member of this group');
        }

        $group->addUser($newUser);

        $this->groupRepository->save($group);

        return new JsonResponse([
            'message' => \sprintf('User with id %s has been added to group with id %s',
            $newUser->getId(),
            $group->getId()),
        ]);
    }
}
