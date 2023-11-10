<?php

namespace Core\Database\Query;

use Core\Support\QueryTemplatePartInterface;

/**
 * potential alternative way
 */
abstract class _BaseQueryTemplatePart implements QueryTemplatePartInterface
{

    public const        TEMPLATE = '';

    protected array     $values    = [];
    protected string    $sql       = '';

    public function __construct()
    {
        if (!static::TEMPLATE)
            throw new \LogicException("[".static::class."]: template cannot be empty");
    }

    protected function add(string|QueryTemplatePartInterface ...$values): void
    {
        $this->values = array_merge($this->values, $values);
        $this->sql = '';
    }

    public function __toString(): string
    {
        if ($this->sql)
            return $this->sql;

        $replaces = $this->preprocessValues();
        $this->sql = sprintf(static::TEMPLATE, ...$replaces);

        return $this->sql;
    }

    protected function preprocessValues(): array
    {
        return [implode(',', $this->values)];
    }

    abstract public function write();
}
