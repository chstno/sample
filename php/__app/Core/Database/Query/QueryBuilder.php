<?php


namespace Core\Database\Query;

use Core\Exception\BadMethodCallException;
use Core\Support\ExpressionInterface;
use Core\Support\QueryBuilderInterface;
use Core\Support\QueryTemplateEngineInterface;


/**
 * Class QueryBuilder
 *
 * @package Core\Database\Query
 *
 **/

abstract class QueryBuilder implements QueryBuilderInterface
{

    protected QueryTemplateEngineInterface $queryTemplateEngine;

    // definitely, it is not the best solution, but injecting class as object seems even worse
    // or pass it through Container dependency as string
    /**
     * @var class-string<ExpressionInterface>
     */
    protected string    $expr           = BaseExpression::class;
    protected array     $params         = [];
    protected int       $lastParamId    = -1;


    public function __construct(QueryTemplateEngineInterface $templateEngine)
    {
        $this->queryTemplateEngine = $templateEngine;
        if (!is_subclass_of($this->expr, ExpressionInterface::class))
            throw new \InvalidArgumentException("Incorrect expression class at query-builder [" . static::class . "]");
    }


    /*
     * alternative (not true) way to avoid code duplications
     *
    public function __call($name, $args)
    {
        if (method_exists($this->queryTemplateEngine, $name)) {
            $this->queryTemplateEngine->$name(...$args);
            return $this;
        } else {
            throw new BadMethodCallException(static::class, $name);
        }
    }
    */

    public function clean(): void
    {
        $this->cleanParameters();
        $this->queryTemplateEngine->clean();
    }

    public function __clone()
    {
        $this->queryTemplateEngine = clone $this->queryTemplateEngine;
    }

    public function __toString(): string
    {
        return (string) $this->queryTemplateEngine;
    }

    public function expr(): BaseExpression
    {
        return new $this->expr();
    }

    public function getParameters(bool $clean = true): array
    {
        $params = $this->params;
        if ($clean) $this->cleanParameters();
        return $params;
    }

    public function setParametersValues(array $parameters): void
    {

        if (!$this->params)
            throw new \RuntimeException("[".static::class."]: cannot fill empty parameters with only values");

        if (count($parameters) !== count($this->params))
            throw new \RuntimeException("[".static::class."]: cannot auto-fill parameter values: the number of elements does not match");

        reset($parameters);

        foreach ($this->params as $name => $value) {
            $this->params[$name] = current($parameters);
            next($parameters);
        }

    }

    public function cleanParameters(): void
    {
        $this->params = [];
        $this->lastParamId = -1;
    }

    public function valuesToNamedParameters(mixed &...$values): array
    {
        foreach ($values as $key => $value) {
            $values[$key] = $this->setNamedParameter($value);
        }

        return $values;
    }

    public function prepareTableFields(array &$fields, string $table, string $childTable = ''): array
    {
        foreach ($fields as $key => $field) {

            if (is_int($key)) {
                $_key = $key;
                $field = (string) $this->expr()->field($field, $table);
            } else {
                $_key = (string) $this->expr()->field($key, $table);
                $field = $childTable ? (string) $this->expr()->field($field, $childTable) : $field;
                unset($fields[$key]);
            }

            $fields[$_key] = $field;
        }

        return $fields;
    }

    public function prepareConditionFromArray(
        array   $keys,
        string  $concatenationOperator = '&&',
        string  $operation = '=',
    ): ExpressionInterface
    {
        /**
         * @var $expr BaseExpression
         */
        $expr = $this->expr();

        foreach ($keys as $key => $value) {

            if ((string) $expr)
                $expr->$concatenationOperator();

            if (is_int($key) && is_array($value) && count($value) === 3) {
                $key = $value[0];
                $customOperation = (string) $value[1];
                $value = $value[2];
            }

            if (is_numeric($value) || $this->isLiteral($value))
                $value = $this->setNamedParameter(trim($value, "\"'"));

            if (!isset($customOperation))
                $expr->$operation($key, $value);
            else
                $expr->$customOperation($key, $value);
        }

        return $expr;
    }

    public function isLiteral(mixed $expr): bool
    {
        return trim($expr, "\"'") !== $expr;
    }

    public function from(string $table, string $alias = null): static
    {
        $expr = $this->expr()->table($table);
        if ($alias) $expr->alias($alias);

        $this->queryTemplateEngine->from($table);
        return $this;
    }

    public function select(string ...$select): static
    {
        $this->queryTemplateEngine->select(...$select);
        return $this;
    }

    public function addSelect(string ...$select): static
    {
        $this->queryTemplateEngine->addSelect(...$select);
        return $this;
    }

    public function update(string $table): static
    {
        $this->queryTemplateEngine->update($table);
        return $this;
    }

    public function insert(string $table, array $fields): static
    {
        $this->queryTemplateEngine->insert($table, $fields);
        return $this;
    }

    public function limit(int $limit, int $offset = 0): static
    {
        $this->queryTemplateEngine->limit($limit, $offset);
        return $this;
    }

    public function order(string ...$order): static
    {
        $this->queryTemplateEngine->order(...$order);
        return $this;
    }

    public function delete(): static
    {
        $this->queryTemplateEngine->delete();
        return $this;
    }

    public function union(string $union): static
    {
        $this->queryTemplateEngine->union($union);
        return $this;
    }

    abstract public function with(string $table, array $keys): static;
    abstract public function selectWithTablePrefix(array $select): static;
    abstract public function setNamedParameter(mixed $value): string;
}