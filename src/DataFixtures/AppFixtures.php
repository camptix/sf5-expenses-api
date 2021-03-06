<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\User;
use App\Security\Role;
use App\Service\Password\EncoderService;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    private EncoderService $encoderService;

    public function __construct(EncoderService $encoderService)
    {
        $this->encoderService = $encoderService;
    }

    /**
     * @throws \Exception
     */
    public function load(ObjectManager $manager)
    {
        $users = $this->getUsers();
        foreach ($users as $userData) {
            $user = new User($userData['name'], $userData['email'], $userData['id']);
            $user->setPassword($this->encoderService->generateEncodedPasswordForUser($user, $userData['password']));
            $user->setRoles($userData['roles']);

            $manager->persist($user);

            foreach ($userData['groups'] as $groupData) {
                $group = new Group($groupData['name'], $user, $groupData['id']);
                $group->addUser($user);

                $manager->persist($group);
            }
        }

        $manager->flush();
    }

    private function getUsers(): array
    {
        return [
            [
                'id' => '74099578-74e0-4d3e-9e67-a2d666b4a001',
                'name' => 'Admin',
                'email' => 'admin@api.com',
                'password' => 'password',
                'roles' => [
                    Role::ROLE_ADMIN,
                    Role::ROLE_USER,
                ],
                'groups' => [
                    [
                        'id' => '74099578-74e0-4d3e-9e67-a2d666b4a003',
                        'name' => 'Admin\'s Group',
                    ],
                ],
            ],
            [
                'id' => '74099578-74e0-4d3e-9e67-a2d666b4a002',
                'name' => 'User',
                'email' => 'user@api.com',
                'password' => 'password',
                'roles' => [Role::ROLE_USER],
                'groups' => [
                    [
                        'id' => '74099578-74e0-4d3e-9e67-a2d666b4a004',
                        'name' => 'User\'s Group',
                    ],
                ],
            ],
        ];
    }
}
