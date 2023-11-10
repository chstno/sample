<?php


namespace Core\Database\Trait;


use Core\Model\Model;
use Core\Repository\ModelRepository;
use Core\Support\QueryBuilderInterface;


trait Relation
{
    /**
     * @var array<class-string, QueryBuilderInterface>
     */
    protected array $relations = [];
    protected array $relationRepositories = [];

    /**
     * Can be implemented via using other trait,
     * that declares properties and default constructor.
     *
     *
     */
    abstract public function getModelClass(): string;
    abstract public function getDtoMap(): array;
    abstract public function prepareModelAttributes(Model $model, bool $primary = false): array;

    abstract protected function setRelation(string $relationKey, ModelRepository $childRepository);
    abstract protected function getRelation(string $relationKey);
}