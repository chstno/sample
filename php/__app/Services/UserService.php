<?php

namespace Services;

use Models\User;
use Support\UserRepositoryInterface;
use Support\UserServiceInterface;

class UserService implements UserServiceInterface
{
    public function test(int $userId): iterable
    {
        /*
         * business-logic level of code
         *  ...
         */

        /**
         * @var UserRepositoryInterface $userRepository
         */
        $userRepository = instance(UserRepositoryInterface::class);
        $user = new User();
        $user->id = $userId;

        return [
            'join' => $userRepository->getPosts($user),
            'cross' => $userRepository->getPostsViaCrossRelation($user),
            'usersUnder20' => $userRepository->getByUnderAge(20),
            'usersOlderThan20' => $userRepository->getOlderThan(20),
        ];
    }
}