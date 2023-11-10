<?php

namespace Support;

interface UserServiceInterface
{
    public function test(int $userId): iterable;
}