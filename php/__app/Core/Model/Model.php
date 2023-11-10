<?php


namespace Core\Model;

use Core\Autoloader;
use Core\DTO\BaseDTO;

/**
 * Class Model
 *
 * Generally speaking, the main idea is to isolate models from the implementation of data access.
 * They are responsible for the level of "business logic",
 * and their attributes can be represented in different ways in the sources
 *
 * in terms of DDD - it's entity
 *
 * @package Core\Models
 */

abstract class Model
{
    /**
     * @var array automatically filled with protected fields
     */
    protected static array $primary = [];

    /**
     * @var array $relations
     *
     * ['relatedClass' => ['column1' => 'relatedColumn1', 'column2' => 'relatedColumn2' ...], 'relatedClass2' => ...]
     */
    protected static array $relations = [];

    /**
     * Runs only once, on class load
     *
     * @see Autoloader
     */
    public static function __onAutoload()
    {
        if (array_key_exists(0, static::$primary)) {
            static::$primary = array_fill_keys(static::$primary, 1); // faster checking for existence
        }
    }

    public static function getPrimary(): array
    {
        return static::$primary;
    }

    public function getPrimaryKeys(): array
    {
        $primaryKeys = [];
        foreach (static::$primary as $primaryKey => $v) {

            if (!isset($this->$primaryKey)) // it makes sense to return only when all keys are given (as i think)
                return [];

            $primaryKeys[$primaryKey] = $this->$primaryKey;
        }

        return $primaryKeys;
    }

    public function getAttributes(bool $includePrimary = false): array
    {
        // $vars = get_object_vars($this); sees protected properties too.. so a little hack right there
        $vars = \Closure::fromCallable('get_object_vars')->__invoke($this);
        if (!$includePrimary) {
            // cause foreach is faster than array_filter
            foreach ($vars as $attr => $value) {
                if (isset(static::$primary[$attr]))
                    unset($vars[$attr]);
            }
        }

        return $vars;
    }

    // uses only existing fields
    public function fill(array|BaseDTO $attributes, array $map = []): static
    {
        if ($attributes instanceof BaseDTO) {
            $attributes = $attributes->getData();
            $map = [];
        }

        foreach ($attributes as $attr => $value) {
            $attr = $map[$attr] ?? $attr;
            if (property_exists($this, $attr) && !property_exists(self::class, $attr)) {
                $this->$attr = $value;
            }
        }

        return $this;
    }

    public static function getFields(array $map = []): array
    {
        $vars = get_class_vars(static::class);

        if ($map) {
            foreach ($vars as $name => $value) {
                $vars[$name] = $map[$name] ?? $name;
            }
        }

        return $vars;
    }

    public static function getRelations(?string $class): array
    {
        return $class ? (static::$relations[$class] ?? []) : static::$relations;
    }
}