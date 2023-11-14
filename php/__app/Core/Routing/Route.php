<?php


namespace Core\Routing;


use Core\Support\RouteInterface;

/**
 * Class Route
 *
 * @Warning: matching is case-insensitive
 * @package Core\Routing
 */

class Route implements RouteInterface
{
    protected string    $path;
    protected string    $pathRegex;
    protected array     $parameters;

    /**
     * @var callable
     */
    protected $action;

    public const PARAM_REGEX = '#\{([^/]+)\}#';
    public const PARAM_REPLACE_REGEX = '(?P<%s>[^/]++)';
    public const PATH_REGEX = '#^%s$#i';

    public function __construct(string $path, callable $action)
    {
        $this->parseUri($path);
        $this->action = $action;
    }

    protected function parseUri(string $path): void
    {
        $this->path = $path;
        $path = trim($path, '/');
        $parameterReplaces = [];
        preg_match_all(static::PARAM_REGEX, $path, $params);

        if ($params) {
            $this->parameters = array_fill_keys($params[1], null);

            foreach ($params[0] as $key => $parameter) {
                $parameterReplaces[$parameter] = sprintf(static::PARAM_REPLACE_REGEX, $params[1][$key]);
            }
        }

        $this->pathRegex = sprintf(static::PATH_REGEX, strtr($path, $parameterReplaces));
    }

    public function match(string $path): bool
    {
        $path = trim($path, '/');
        if (preg_match($this->pathRegex, $path, $match)) {
            foreach ($this->parameters as $name => $value) {
                $this->parameters[$name] = urldecode($match[$name]);
            }
            return true;
        }

        return false;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getAction(): callable
    {
        return $this->action;
    }
}