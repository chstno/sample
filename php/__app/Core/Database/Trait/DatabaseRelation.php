<?php


namespace Core\Database\Trait;


use Core\Database\DBConnection;
use Core\Database\Query\QueryBuilder;
use Core\Database\Query\SQLQueryBuilder;
use Core\Model\Model;
use Core\Repository\ModelDatabaseRepository;
use Core\Repository\ModelRepository;

/**
 * trait DatabaseRelation
 *
 * @package Core\Database
 *
 */

trait DatabaseRelation
{
    use Relation;

    abstract public function getTable(): string;
    abstract public function getConnection(): DBConnection;
    abstract public function getQueryBuilder(): QueryBuilder;

    protected function setDatabaseRelation(string $relationKey, ModelRepository $childRepository): QueryBuilder
    {

        if (!$childRepository instanceof ModelDatabaseRepository)
            throw new \RuntimeException("[".static::class."]: relations for other repository types are not yet implemented");


        if ($this->getConnection() !== $childRepository->getConnection())
            throw new \RuntimeException(
                sprintf(
                    "[%s]: relations for different connections not possible, use [%s] instead",
                    static::class,
                    CrossSourcesRelation::class
                ));


        $parentModel = $this->getModelClass();
        $parentTable = $this->getTable();
        $parentDto = $this->getDtoMap();
        $childModel = $childRepository->getModelClass();
        $childTable = $childRepository->getTable();
        $childDto = $childRepository->getDtoMap();

        /**
         * @var class-string<Model>|Model $parentModel // "|Model" purely added for phpstorm, in reality it is string
         * @method getRelations()
         */
        $keys = $parentModel::getRelations($childModel);
        $queryBuilder = clone $this->getQueryBuilder();
        /*$queryBuilder->clean();*/

        if (!$keys) {
            throw new \LogicException("Relation [{$parentModel}]->[$childModel] was not found!");
        }

        $relationKeys = [];

        foreach ($keys as $parentKey => $childKey) {
            $parentKey = (string) $queryBuilder->expr()->field($parentDto[$parentKey] ?? $parentKey, $parentTable);
            $childKey = (string) $queryBuilder->expr()->field($childDto[$childKey] ?? $childKey, $childTable);
            $relationKeys[$parentKey] = $childKey;
        }

        unset($keys);

        //todo: refactor
        $fields[] = $queryBuilder->expr()->field('*', $parentTable);
        $fields[] = $queryBuilder->expr()->field('*', $childTable);

        $queryBuilder
            ->select(...$fields)
            ->from($parentTable)
            ->innerJoin($childTable, $relationKeys);


       $this->setRelationRepository($relationKey, $childRepository);

        return $this->setRelation($relationKey, $queryBuilder);
    }

    protected function has(Model $model, string $relatedRepositoryClass): array
    {
        $relationQuery = $this->getRelation($relatedRepositoryClass);

        if ($relationQuery === null)
            $relationQuery = $this->setDatabaseRelation($relatedRepositoryClass, instance($relatedRepositoryClass));

        $modelKeys = $this->prepareModelAttributes($model, true);
        $this->getQueryBuilder()->prepareTableFields($modelKeys, $this->getTable());
        /**
         * @var SQLQueryBuilder $relationQuery
         */
        $relationQuery = $relationQuery->where($modelKeys);
        $data = $this->getConnection()->query($relationQuery, $relationQuery->getParameters());

        return $this->getRelationRepository($relatedRepositoryClass)->dataToModels($data);
    }
}