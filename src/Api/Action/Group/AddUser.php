<?php

declare(strict_types=1);

namespace App\Api\Action\Group;

use App\Api\Action\RequestTransformer;
use App\Entity\User;
use App\Exception\Group\CannotAddUsersToGroupException;
use App\Exception\Group\GroupDoesNotExistException;
use App\Exception\Group\UserAlreadyMemberOfGroupException;
use App\Exception\Group\UserDoesNotExistException;
use App\Repository\GroupRepository;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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
     */
    public function __invoke(Request $request, User $user): JsonResponse
    {
        //En este nivel symfony no nos permite recuperar el tokenStorage por lo que esto sería nulo
        //Para resolverlo usamos un argumentResolver
        //$user = $this->tokenStorage->getToken()->getUser();
        $groupId = RequestTransformer::getRequiredField($request, 'group_id');
        $userId = RequestTransformer::getRequiredField($request, 'user_id');

        if (null === $group = $this->groupRepository->findOneById($groupId)) {
            throw GroupDoesNotExistException::fromGroupId($groupId);
        }

        if (!$this->groupRepository->userIsMember($group, $user)) {
            throw CannotAddUsersToGroupException::create();
        }

        if (null === $newUser = $this->userRepository->findOneById($userId)) {
            throw UserDoesNotExistException::fromUserId($userId);
        }

        //Si el usuario ya existe en este grupo lanzamos una excepcion
        if ($this->groupRepository->userIsMember($group, $newUser)) {
            throw UserAlreadyMemberOfGroupException::fromUserId($userId);
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