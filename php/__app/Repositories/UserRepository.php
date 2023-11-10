<?php


namespace Repositories;


use Core\App;
use Core\Database\DBConnection;
use Core\Database\Query\QueryBuilder;
use Core\Database\Query\SQLQueryBuilder;
use Core\Database\Trait\DatabaseRelation;
use Core\Model\Model;
use Core\Repository\ModelDatabaseRepository;
use DTO\UserDatabaseDTO;
use Models\Post;
use Models\User;
use Support\PostRepositoryInterface;
use Support\UserRepositoryInterface;

/**
 * Class UserRepository
 *
 * At very specific situations we will be able to change the parent class
 * thereby replacing the source and without affecting the business logic (domain level)
 *
 * @package Repositories
 */
class UserRepository extends ModelDatabaseRepository implements UserRepositoryInterface
{

    use DatabaseRelation;

    protected string $modelClass    = User::class;
    protected string $dtoClass      = UserDatabaseDTO::class;
    protected string $table         = 'users';

    public function __construct(DBConnection $connection, QueryBuilder $queryBuilder)
    {
        parent::__construct($connection, $queryBuilder);
    }

    // todo: refactor setting relation
    public function getPosts(User $user): iterable
    {
        return $this->has($user, PostRepositoryInterface::class);
    }

    public function getByUnderAge(int $age): iterable
    {
        // TODO: Implement getByUnderAge() method.
    }

    public function getOlderThan(int $age): iterable
    {
        // TODO: Implement getByOlderThan() method.
    }
}