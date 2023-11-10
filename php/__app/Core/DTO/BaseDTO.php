<?php


namespace Core\DTO;


// todo: needed from and to, cause we transfer object to the source too?

use Core\Exception\BadAccessProperty;

abstract class BaseDTO
{
    /**
     * @var array $map consists of 'sourceProperty(from)' => 'ModelProperty(to)'
     *
     * Yeah, it's not "canon", but from my point of view - much better and easier to use
     *
     * most of the time, I used it as a mapper,
     * but the implementation allows to use it as an immutable object
     */

    protected static array  $map = [];

    protected array         $attributes = [];
    protected bool          $immutability = false;

    public function __construct(array|object $values)
    {
        if (is_object($values))
            $values = get_object_vars($values);


        // also, possibly do initialization from some className, and match by order
        // (so not needed to modify dto after changes in model attributes)

        // left that realisation to simplify solution

        foreach ($values as $key => $value) {
            $reformatMethod = "__{$key}"; // can be used as internal validation/reformat to both-sides
            if (method_exists($this, $reformatMethod))
                $value = $this->$reformatMethod($value);
            $key = static::$map[$key] ?? $key;
            $this->$key = $value;
        }

        $this->immutability = true;
    }

    public static function __onAutoload()
    {
        // allows us to use both directions of "moving"
        // and seems like a bad practice, but it works
        static::initMap();
    }
    
    protected static function initMap()
    {
        foreach (static::$map as $sourceAttr => $modelAttr) {
            static::$map[$modelAttr] = $sourceAttr;
        }
    }

    public function __set(string $name, $value)
    {
        if (!$this->immutability) {
            $this->attributes[$name] = $value;
        } else {
            throw new \LogicException("Error while trying to change immutable [" . static::class . "]");
        }
    }

    public function __get(string $name)
    {
        if (!isset($this->attributes[$name]))
            throw new BadAccessProperty(static::class, $name);

        return $this->attributes[$name];
    }

    public function getData(bool $asObject = false): array|object
    {
        if ($asObject)
            return (object) $this->attributes;
        else
            return $this->attributes;
    }

    public static function getMap(): array
    {
        return static::$map;
    }
}