<?php


namespace DTO;


use Core\DTO\BaseDTO;

class PostDatabaseDTO extends BaseDTO
{
    protected static array $map = [
        'id' => 'id',
        'user_id' => 'userId',
        'text' => 'text',
    ];
}