<?php


namespace Core\Repository;


use Core\Database\DBConnection;
use Core\Database\FetchTypes;
use Core\Database\Query\QueryBuilder;
use Core\Model\Model;

/**
 * Class ModelDatabaseRepository
 *
 * @package Core\Repository
 */

class ModelDatabaseRepository extends ModelRepository
{

    protected DBConnection $connection;
    protected QueryBuilder $queryBuilder;

    protected string $table = '';

    public function __construct(DBConnection $connection, QueryBuilder $queryBuilder)
    {
        parent::__construct();

        $this->connection = $connection;
        $this->queryBuilder = $queryBuilder;
    }

    public function getQueryBuilder(): QueryBuilder
    {
        return $this->queryBuilder;
    }

    public function getConnection(): DBConnection
    {
        return $this->connection;
    }

    public function getTable(): string
    {
        return $this->table;
    }

    public function setTable(string $table): void
    {
        $this->table = $table;
    }

    public function prepareLiterals(array &$keys): array
    {
        foreach ($keys as $name => $value) {
            $keys[$name] = (string) $this->queryBuilder->expr()->literal($value);
        }

        return $keys;
    }

    public function find(Model $entity, ?int $limit = null, int $offset = 0): array
    {
        $keys = $this->prepareModelAttributes($entity, true);
        $this->prepareLiterals($keys);

        $this->queryBuilder
            ->select('*')
            ->from($this->table)
            ->where($keys);

        if ($limit !== null)
            $this->queryBuilder->limit($limit, $offset);

        $data = $this->connection->query($this->queryBuilder,
                                         $this->queryBuilder->getParameters());


        return $this->dataToModels($data);
    }

    protected function add(Model $entity): int
    {
        $fields = $this->prepareModelAttributes($entity);
        $this->queryBuilder
            ->insert($this->table, array_keys($fields))
            ->values(...$fields);

        return $this->connection->query($this->queryBuilder,
                                        $this->queryBuilder->getParameters(),
                                        FetchTypes::LAST_INSERT_ID);
    }

    public function save(Model $entity): int
    {
        if ($this->find($entity, 1)) {
            // todo: refactor
            if (!$keys = $entity->getPrimaryKeys())
                throw new \InvalidArgumentException(sprintf("[%s]:[update]: Model-entity [%s] is expected to have specified all primary keys", static::class, get_class($entity)));

            $keys = $this->prepareModelAttributes($keys);
            $this->prepareLiterals($keys);
            $fields = $this->prepareModelAttributes($entity->getAttributes());
            $this->prepareLiterals($fields);

            $this->queryBuilder
                ->update($this->table)
                ->set($fields)
                ->where($keys);

            return $this->connection->query($this->queryBuilder,
                                            $this->queryBuilder->getParameters(),
                                            FetchTypes::AFFECTED);

        } else {
            return $this->add($entity);
        }
    }

    public function delete(Model $entity): int
    {
        if (!$keys = $this->prepareModelAttributes($entity, true))
            throw new \InvalidArgumentException(sprintf("[%s]:[delete]: Model-entity [%s] is expected to be not empty", static::class, get_class($entity)));

        $this->queryBuilder
            ->delete()
            ->from($this->table)
            ->where($keys);

        return $this->connection->query($this->queryBuilder,
                                        $this->queryBuilder->getParameters(),
                                        FetchTypes::AFFECTED);
    }
}