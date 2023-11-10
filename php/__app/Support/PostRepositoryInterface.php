<?php


namespace Support;


use Models\Post;
use Models\User;

interface PostRepositoryInterface
{
    public function find(Post $entity, ?int $limit = null, int $offset = 0): iterable;
    public function save(Post $entity): int;
    public function delete(Post $entity): int;
    public function getUser(Post $post): ?User;
}