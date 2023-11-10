<?php

namespace Core\Trait;

use Core\Exception\BadMethodCallException;

/**
 * @property array $aliases ['aliasName' => 'actualFunction']
 */
trait MethodsAliases
{
    public function __call($name, $args)
    {
        $alias = $this->aliases[trim($name)] ?? null;
        if ($alias && method_exists($this, $alias)){
            return $this->$alias(...$args);
        } else {
            throw new BadMethodCallException(static::class, $name);
        }
    }
}