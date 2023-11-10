<?php


namespace Core\Support;


interface ContainerInterface
{
    public function get(string $class, &...$args);
    public function set(string $class, string|callable|object $bind);
    public function resolve(string $class, &...$args);
}