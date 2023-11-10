<?php

namespace Core\Trait;

use Core\Exception\BadAccessProperty;

trait PropertyGetAccessor
{
    public function __get($name)
    {
        if (property_exists($this, $name))
            return $this->$name;
        else
            throw new BadAccessProperty(static::class, $name);
    }
}