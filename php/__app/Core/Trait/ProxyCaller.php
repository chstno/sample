<?php


namespace Core\Trait;

use Core\Exception\BadMethodCallException;
use Throwable;

/**
 * Trait ProxyCaller
 *
 * Used in the past to redirect calls from the query builder to the template engine,
 * so as not to duplicate a large number of methods
 * (+create a separate interface).
 *
 * This creates implicit behavior, which is why it is now in @deprecated status
 */
trait ProxyCaller
{
    private string $proxyPropertyName;

    public function __call($name, $args)
    {
        try {
            // if proxied property also uses proxy, we cannot rely on method_exists() or is_callable()
            return $this->{$proxyPropertyName}->$name($args);
        } catch (Throwable $e) {
            throw new BadMethodCallException(static::class, $name);
        }
    }
}