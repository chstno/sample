<?php

namespace Core\Database\Trait;

use Core\Model\Model;
use Core\Repository\ModelRepository;

/**
 * and again, the task was to convey the idea, and not to try to embrace the immensity
 */
trait CrossSourcesRelation
{
    use Relation;

    protected function setCrossRelation(string $relationKey, ModelRepository $childRepository): callable
    {

        $parentModel = $this->getModelClass();
        $childModel = $childRepository->getModelClass();

        /**
         * @var class-string<Model>|Model $parentModel
         */
        $keys = $parentModel::getRelations($childModel);

        if (!$keys) {
            throw new \LogicException("Relation [{$parentModel}]->[$childModel] was not found!");
        }

        $this->setRelationRepository($relationKey, $childRepository);

        // specific cases with multiple objects relation -- must be implemented separately
        // as methods or via complex-repos

        $relation = function (Model $parent, ?callable $filter = null) use ($childRepository, $childModel, $keys) {

            $parentRepositoryRecord = $this->find($parent)[0] ?? null;
            $child = new $childModel();

            foreach ($keys as $parentKey => $childKey) {
                $child->{$childKey} = $parentRepositoryRecord->{$parentKey};
            }

            $findResult = $childRepository->find($child);

            return $filter ? $filter($findResult) : $findResult;
        };

        return $this->setRelation($relationKey, $relation);
    }

    public function has(Model $model, string $relatedRepositoryClass)
    {
        $relation = $this->getRelation($relatedRepositoryClass);

        if ($relation === null)
            $relation = $this->setCrossRelation($relatedRepositoryClass, instance($relatedRepositoryClass));

        return $relation($model);
    }
}