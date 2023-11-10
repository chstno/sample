<?php


namespace Repositories;


use Core\App;
use Core\Database\DBConnection;
use Core\Database\Query\QueryBuilder;
use Core\Database\Query\SQLQueryBuilder;
use Core\Database\Trait\DatabaseRelation;
use Core\Repository\ModelDatabaseRepository;
use DTO\PostDatabaseDTO;
use Models\Post;
use Models\User;
use Support\PostRepositoryInterface;
use Support\UserRepositoryInterface;

class PostRepository extends ModelDatabaseRepository implements PostRepositoryInterface
{

    use DatabaseRelation;

    protected string $modelClass    = Post::class;
    protected string $dtoClass      = PostDatabaseDTO::class;
    protected string $table         = 'posts';

    public function __construct(DBConnection $connection, QueryBuilder $queryBuilder)
    {
        parent::__construct($connection, $queryBuilder);
    }

    public function getUser(Post $post): ?User
    {
        return $this->has($post, UserRepositoryInterface::class)[0] ?? null;
    }
}