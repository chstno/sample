<?php


namespace Core\Support;

interface RouterInterface
{
    public static function handle(RequestInterface $request);
    public static function getRoutes(): array;
    public static function getMethods(): array;
    public static function cleanRoutes(): void;
    public static function route(string $path, array|string $action, string $method): RouteInterface;
}