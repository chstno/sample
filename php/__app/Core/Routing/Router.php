<?php


namespace Core\Routing;


use Core\Exception\RouteNotFoundException;
use Core\Service\ReflectionCaller;
use Core\Support\RequestInterface;
use Core\Support\RouteInterface;
use Core\Support\RouterInterface;
use Core\Support\SingletonInterface;
use Core\Trait\Singleton;

/**
 * Class Router
 *
 * A simple routing class.
 * Yes, it could be implemented through enumerating controllers and attributes (via reflection),
 * but this, in my opinion, is a too resource-demanding method (and also redundant)
 *
 * @package Core\Routing
 */
class Router implements RouterInterface, SingletonInterface
{

    use Singleton;


    public const NOT_FOUND = 404;

    /**
     * Consists of initialized router objects
     *
     * @var array<RouteInterface>
     */
    protected static array $routes;

    /**
     * @var class-string<RouteInterface>
     */
    protected static string $routeClass = Route::class;

    /**
     * References on objects above, but mapped to the request method
     * @var array<array<RouteInterface>>
     */
    protected static array $methodRoutes;
    protected static array $methods = ['GET', 'HEAD', 'POST', 'PUT', 'PATCH', 'DELETE', 'OPTIONS'];


    public static function __onAutoload()
    {
        if (!is_subclass_of(static::$routeClass, RouteInterface::class)) {
            throw new \InvalidArgumentException("[". static::class ."]: incorrect \$routeClass, it must be subclass of " . RouteInterface::class);
        }
    }

    public static function route(string $path, array|string $action, string $method): RouteInterface
    {
        $method = mb_strtoupper($method);
        // allows using one route-object to different methods
        if (!isset(static::$methodRoutes[$method][$path])) {

            is_callable($action, true, $actionId);
            $routeId = "{$path}:{$actionId}";

            if (!isset(static::$routes[$routeId]))
                static::$routes[$routeId] = new static::$routeClass($path, $action);

            return static::$methodRoutes[$method][$path] = &static::$routes[$routeId];

        } else {
            throw new \LogicException("Path [$method]::[{$path}] is already defined!");
        }
    }

    public static function handle(RequestInterface $request): mixed
    {
        $routes = static::$methodRoutes[$request->getMethod()] ?? [];

        foreach ($routes as $route) {
            if ($route->match($request->getPath())) {
                try {
                    return static::runAction($route->getAction(), $route->getParameters());
                } catch (\TypeError $e) {
                    //ignore and look further, perhaps a path with appropriate names/types has been defined
                    continue;
                } catch (\Throwable $e) {
                    // if argument types does not match
                    throw new RouteNotFoundException("Not found: " . self::NOT_FOUND, 0, $e);
                }
            }
        }

        throw new RouteNotFoundException("Not found: " . self::NOT_FOUND);
    }

    public static function getRoutes(): array
    {
        return static::$methodRoutes;
    }

    public static function getMethods(): array
    {
        return static::$methods;
    }

    public static function cleanRoutes(): void
    {
        static::$routes = [];
        static::$methodRoutes = [];
    }

    public static function runAction(callable $action, array $parameters)
    {
        /**
         * @Warning! parameter names must be strictly equal and types are castable!
         */
        return ReflectionCaller::callFunctionWithNamedParams($action, $parameters);
    }

    public static function any(string $path, array|string $action): RouteInterface
    {
        foreach (static::$methods as $method) {
            static::$method($path, $action);
        }

        return static::$methodRoutes[static::$methods[0]][$path];
    }

    public static function get(string $path, array|string $action): RouteInterface
    {
        return static::route($path, $action, 'GET');
    }

    public static function head(string $path, array|string $action): RouteInterface
    {
        return static::route($path, $action, 'HEAD');
    }

    public static function post(string $path, array|string $action): RouteInterface
    {
        return static::route($path, $action, 'POST');
    }

    public static function put(string $path, array|string $action): RouteInterface
    {
        return static::route($path, $action, 'PUT');
    }

    public static function delete(string $path, array|string $action): RouteInterface
    {
        return static::route($path, $action, 'DELETE');
    }

    public static function patch(string $path, array|string $action): RouteInterface
    {
        return static::route($path, $action, 'PATCH');
    }

    public static function options(string $path, array|string $action): RouteInterface
    {
        return static::route($path, $action, 'OPTIONS');
    }
}