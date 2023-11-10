<?php


namespace Core\Repository;


use Core\DTO\BaseDTO;
use Core\Model\Model;

/**
 * Class ModelRepository
 *
 * Any specific data manipulation (crud)
 * will be on that layer, others just using it
 *
 * @package Core\Repository
 *
 */

abstract class ModelRepository
{
    /**
     * @var class-string<Model> $modelClass
     */
    protected string $modelClass = Model::class;
    /**
     * @var class-string<BaseDTO> $dtoClass
     */
    protected string $dtoClass = BaseDTO::class;
    protected array  $dtoMap = [];

    public function __construct()
    {
        if (!is_subclass_of($this->modelClass, Model::class)) {
            throw new \InvalidArgumentException("Incorrect model class for [" . static::class . "]");
        }

        if (!is_subclass_of($this->dtoClass, BaseDTO::class)) {
            throw new \InvalidArgumentException("Incorrect dto-class for [" . static::class . "]");
        }

        $this->dtoMap = $this->dtoClass::getMap();
    }

    public function getDtoMap(): array
    {
        return $this->dtoMap;
    }

    public function getModelClass(): string
    {
        return $this->modelClass;
    }

    /**
     *
     * Converts model attributes via dto object
     *
     * @param Model|array $entity
     * @param bool $primary
     *
     * @return array
     */
    public function prepareModelAttributes(Model|array $entity, bool $primary = false): array
    {
        if ($primary && $entity instanceof Model && $keys = $entity->getPrimaryKeys())
            $entity = $keys;

        return (new $this->dtoClass($entity))->getData(); // convert names and data to expected format
    }

    /**
     * Makes array of model-objects from data rows
     *
     * @param array $dataRows
     *
     * @return array
     */
    public function dataToModels(array &$dataRows): array
    {
        foreach ($dataRows as $dataRow => $data) {
            $model = new $this->modelClass();
            $dtoObj = new $this->dtoClass($data);
            $model->fill($dtoObj);
            $dataRows[$dataRow] = $model;
        }

        return $dataRows;
    }
}