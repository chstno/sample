<?php


namespace Core\Database\Trait;


use Core\Model\Model;
use Core\Repository\ModelRepository;
use Core\Support\QueryBuilderInterface;


trait Relation
{
    /**
     * @var array<class-string, mixed>
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


    protected function getRelation(string $relationKey): mixed
    {
        return isset($this->relations[$relationKey]) ? clone $this->relations[$relationKey] : null;
    }

    public function setRelation(string $relationKey, mixed $relation): mixed
    {
        return $this->relations[$relationKey] = $relation;
    }

    protected function getRelationRepository(string $relationKey): ?ModelRepository
    {
        return $this->relationRepositories[$relationKey] ?? null;
    }

    protected function setRelationRepository(string $relationKey, ModelRepository $repository): void
    {
        $this->relationRepositories[$relationKey] = $repository;
    }
}