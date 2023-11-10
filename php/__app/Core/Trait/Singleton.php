<?php

namespace Core\Trait;

trait Singleton
{
    /**
     * @var static $singleton - object of static class, that uses trait
     */
    protected static $singleton = null;

    private function __construct(){}

    public static function getInstance(): static
    {
        if (!isset(static::$singleton))
            static::$singleton = new (static::class)();

        return static::$singleton;
    }
}