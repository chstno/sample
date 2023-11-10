<?php

namespace Core\Service;

use Core\Support\ContainerInterface;
use Core\Support\SingletonInterface;
use Core\Trait\Singleton;

/**
 * Class Container
 *
 * @package Core\Service
 */

//todo: refactor && caching (via something like CachedReflection class)

final class Container implements ContainerInterface, SingletonInterface
{

    use Singleton;

    /**
     * @var array resolved objects and interfaces that matched to classes
     */
    protected array $instances = [];

    /**
     * @throws \ReflectionException
     */
    public function get(string $class, &...$args)
    {
        // it leaves possibility to use already defined dependencies as closures,
        // but, if used in wrong way, can introduce some troubles with cycles

        if (isset($this->instances[$class])) {
            $instance = $this->instances[$class];

            if (is_callable($instance)) { // Closure first
                return $instance();
            } elseif (is_object($instance)) {
                if (!$args) {
                    return $instance;
                } else {
                    unset($this->instances[$class]);
                    return $this->get($class, ...$args); // redefine object if needed
                }
            } elseif (is_string($instance)) {
                return $this->get($instance, ...$args);
            } else
                throw new \LogicException("[". static::class ."] incorrect instance: " . print_r($instance, true));
        }

        $this->instances[$class] = $this->resolve($class, ...$args);
        return $this->instances[$class];
    }

    public function set(string $class, string|callable|object $bind): void
    {
        $this->instances[$class] = $bind;
    }

    public function getInstances(): array
    {
        return $this->instances;
    }

    /**
     *
     * Maybe should passing args recursively?
     * That will allow us to custom init any dependencies
     *
     * @param string $class
     * @param ...$args
     *
     * @return object
     * @throws \ReflectionException
     */
    public function resolve(string $class, &...$args): object
    {
        $reflectionClass  = new \ReflectionClass($class);
        if (!$reflectionClass->isInstantiable()) { // interface or abstract
            throw new \Exception(static::class . " cannot resolve the [{$class}], cause it's not instantiable!");
        }

        $reflectionMethod = $reflectionClass->getConstructor();
        if (!$reflectionMethod)
            return new $class();

        $parameters = $reflectionMethod->getParameters();
        if (!$parameters)
            return new $class();

        /**
         * allows us to pass a custom params (multiple levels)
         */
        reset($args);
        $passingArgs = [];

        if (is_int(key($args))) {
            foreach ($args as $key => $argument) {
                $type = get_debug_type($argument);
                $args[$type] = $argument;
                unset($args[$key]);
            }
        }

        foreach ($parameters as $parameter) {

            $parameterType = $parameter->getType(); // this is correct even for string|null|int etc..
            if (!($parameterType instanceof \ReflectionNamedType) || $parameterType->isBuiltin()) // exclude built-in types (above)
                continue;

            $parameterClass = $parameterType->getName();

            if (($isOptional = $parameter->isOptional()) || isset($args[$parameterClass])) {
                $argValue = $isOptional ? $parameter->getDefaultValue() : $args[$parameterClass];
                $passingArgs[] = $argValue;
                if (!$isOptional) unset($args[$parameterClass]);
                continue;
            }

            $passingArgs[] = $this->get($parameterClass, ...$args);
        }

        return new $class(...$passingArgs);
    }
}