<?php

namespace Core\Trait;

trait AttributesHelper
{
    protected function valuesOrAttributes(&$args)
    {
        if (is_array($args)) {
            foreach ($args as $key => $value) {
                if (property_exists($this, $value)) {
                    $args[$key] = $this->$value ?? static::$$value;
                }
            }
        } elseif (is_string($args)) {
            $args = property_exists($this, $args) ? ($this->$args ?? static::$$args) : $args;
        }

        return $args;
    }

    public function clean($vars = []): void
    {
        $vars = $vars ? $vars : get_class_vars(get_class($this));
        foreach ($vars as $var => $value) {
            if (!isset(static::$$var))
                $this->$var = $value ?? null;
        }
    }
}