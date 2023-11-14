<?php


namespace Core\Database\Query;


use Core\Support\QueryBuilderInterface;
use JetBrains\PhpStorm\Pure;

/**
 * Class SQLQueryBuilder
 *
 * @package Core\Database\Query
 */

class SQLQueryBuilder extends QueryBuilder
{

    public const LEFT_JOIN  = 'LEFT';
    public const RIGHT_JOIN = 'RIGHT';
    public const INNER_JOIN = 'INNER';

    #[Pure]
    public function __construct(SQLQueryTemplateEngine $templateEngine)
    {
        parent::__construct($templateEngine);
    }

    public function leftJoin(string $joinTable, string|array $on): static
    {
        return $this->join($joinTable, $on);
    }

    public function rightJoin(string $joinTable, string|array $on): static
    {
        return $this->join($joinTable, $on, self::RIGHT_JOIN);
    }

    public function innerJoin(string $joinTable, string|array $on): static
    {
        return $this->join($joinTable, $on, self::INNER_JOIN);
    }

    public function join(string $joinTable, string|array $on, string $type = self::LEFT_JOIN): static
    {
        if (is_array($on)) {
            $on = $this->prepareConditionFromArray($on);
        }

        $this->queryTemplateEngine->join($joinTable, $on, $type);
        return $this;
    }

    public function having(array|string $having, string $operator = '&', string $operation = '='): static
    {
        if (is_array($having))
            $having = $this->prepareConditionFromArray($having, $operator, $operation);

        $this->queryTemplateEngine->having($having);
        return $this;
    }

    public function where(array|string $where, string $operator = '&', string $operation = '='): static
    {
        if (is_array($where))
            $where = $this->prepareConditionFromArray($where, $operator, $operation);

        $this->queryTemplateEngine->where($where);
        return $this;
    }

    public function orWhere(array|string $where, string $operator = '&', string $operation = '='): static
    {
        return $this->where([$this->expr()->or(), ...$where], $operator, $operation);
    }

    public function andWhere(array|string $where, string $operator = '&', string $operation = '='): static
    {
        return $this->where([$this->expr()->and(), ...$where], $operator, $operation);
    }

    public function values(string ...$values): static
    {
        $this->valuesToNamedParameters(...$values);
        $this->queryTemplateEngine->values(...$values);
        return $this;
    }

    public function set(array $set): static
    {
        $set = $this->prepareConditionFromArray($set, ',');
        $this->queryTemplateEngine->set($set);
        return $this;
    }

    public function with(string $table, array $keys, array $fields = ['*'], string $parentTable = ''): static
    {
        $parentTable = $parentTable ?? ($this->queryTemplateEngine->table[0] ?? false);

        if (!$parentTable)
            throw new \LogicException("[" . static::class . "]: cannot select with() from nowhere");

        $this->prepareTableFields($fields, $table);
        $this->prepareTableFields($keys, $parentTable, $table);

        $on = $this->prepareConditionFromArray($keys, '&', '=');

        return $this->select(...$fields)->join($table, $on);
    }

    public function selectWithTablePrefix(array $select): static
    {
        $tables = [];
        foreach ($select as $key => $selector) {
            if (is_array($selector)) {
                $table = &$key;
                $tables[] = $table;
                foreach ($selector as $inx => $field) {
                    $selector[$inx] = (string) $this->expr()->field($table, $field);
                }
                array_push($select, ...$selector);
                unset($select[$key]);
                unset($table);
            }
        }

        $this
            ->select(...$select)
            ->from(...$tables); // empty values is also acceptable, we can later call from()

        return $this;
    }

    public function setNamedParameter(mixed $value): string
    {
        $this->lastParamId++;
        $name = "param{$this->lastParamId}";
        $this->params[$name] = $value;
        return $this->expr()->parameter($name);
    }
}