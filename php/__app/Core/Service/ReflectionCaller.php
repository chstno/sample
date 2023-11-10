<?php
declare(strict_types=1);

namespace Core\Service;


use ReflectionFunction;
use ReflectionMethod;

/**
 * Class ReflectorCaller
 *
 * That used in situation, when parameters is mixed (unordered)
 * (dbconnection with config params - as example)
 *
 * @package Core\Services
 */

final class ReflectionCaller
{

    public static function callFunctionWithNamedParams($func, $params)
    {
        try {

            if (is_string($func) && function_exists($func))
                $reflection = new ReflectionFunction($func);
            elseif (is_array($func) && method_exists($func[0], $func[1]))
                $reflection = new ReflectionMethod($func[0], $func[1]);

        } catch (\ReflectionException $e) {
            throw new \BadFunctionCallException("[".self::class."]: Unable to create reflection for callable: " . $e->getMessage());
        }

        if (!isset($reflection))
            throw new \BadFunctionCallException("[".self::class."]: failed! " . is_array($func) ? get_class($func[0]) . '::' . $func[0] : $func . " was not found.");

        $sortedParams = [];
        foreach ($reflection->getParameters() as $parameter) {
            $defaultValue = $parameter->isOptional() ? $parameter->getDefaultValue() : null;
            $sortedParams[] = $params[$parameter->name] ?? $defaultValue;
        }

        return call_user_func($func, ...$sortedParams);
    }
}