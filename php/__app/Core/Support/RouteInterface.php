<?php


namespace Core\Support;


interface RouteInterface
{
    public function getParameters(): array;
    public function getAction(): callable;
    public function match(string $path): bool;
}