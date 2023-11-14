<?php


namespace Repositories;


use Core\Database\DBConnection;
use Core\Database\FetchTypes;
use Core\Database\Query\QueryBuilder;
use Core\Database\Trait\CrossSourcesRelation;
use Core\Database\Trait\DatabaseRelation;
use Core\Model\Model;
use Core\Repository\ModelDatabaseRepository;
use DTO\UserDatabaseDTO;
use Models\User;
use PhpParser\Node\Expr\AssignOp\Mod;
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

    use DatabaseRelation {
        DatabaseRelation::has insteadof CrossSourcesRelation;
    }

    use CrossSourcesRelation {
        CrossSourcesRelation::has as crossHas;
    }

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

    // technically, this is implemented for different data sources, but for now it is demonstrated through the same
    //todo: implement with other entities

    public function getPostsViaCrossRelation(User $user)
    {
        $crossRelationKey = "_" . PostRepositoryInterface::class;
        $this->setCrossRelation($crossRelationKey, instance(PostRepositoryInterface::class));
        return $this->getRelation($crossRelationKey)($user);
    }

    /**
     * to demonstrate the ability to write code abstracted from the implementation
     *
     *
     * cause that syntax is also available
     *
     *  $this->queryBuilder
     *      ->select('*')
     *      ->from($this->table)
     *      ->where("birth_date > {$underTimestamp}");
     *
     */
    protected function getByTimestamp(int $timestamp, string $operation): iterable
    {
        /**
         * @var object<User>|User $modelFields - it's only for ide
         */
        $modelFields = (object) $this->modelClass::getFields($this->getDtoMap());
        $birthDateFieldName = $modelFields->birthDate;

        $where = [[$birthDateFieldName, $operation, $timestamp]];

        $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where($where);

        $data = $this->getConnection()->query($this->queryBuilder, $this->queryBuilder->getParameters());
        return $this->dataToModels($data);
    }

    public function getByUnderAge(int $age): iterable
    {
        $timestamp = strtotime("-{$age} year", time());
        return $this->getByTimestamp($timestamp, $this->queryBuilder->expr()->bigger());
    }

    public function getOlderThan(int $age): iterable
    {
        $timestamp = strtotime("-{$age} year", time());
        return $this->getByTimestamp($timestamp, $this->queryBuilder->expr()->lower());
    }

}