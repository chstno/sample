<?php


namespace Models;


use Core\Model\Model;

/**
 * Class User
 *
 * @package Models
 */

class User extends Model
{

    public ?int         $id;
    public string       $name;
    public int          $gender;
    public \DateTime    $birthDate;

    protected static array $relations   = [Post::class => ['id' => 'userId']];
    protected static array $primary     = ['id'];

}