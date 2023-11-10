<?php


namespace Support;


use Models\User;

interface UserRepositoryInterface
{
    public function find(User $entity, ?int $limit = null, int $offset = 0): iterable;
    public function save(User $entity): int;
    public function delete(User $entity): int;
    public function getByUnderAge(int $age): iterable;
    public function getOlderThan(int $age): iterable;
    public function getPosts(User $user): iterable;
}