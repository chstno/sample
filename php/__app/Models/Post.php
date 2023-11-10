<?php


namespace Models;


use Core\Model\Model;

/**
 * Class Post
 *
 * @package Models
 * @property int $id;
 */

class Post extends Model
{

    protected int   $id;
    public int      $userId;
    public string   $text;

    protected static array $relations   = [User::class => ['userId' => 'id']];
    protected static array $primary     = ['id'];
}