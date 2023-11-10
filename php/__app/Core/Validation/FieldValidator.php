<?php


namespace Core\Validation;


use Core\Exception\BadMethodCallException;
use Core\Support\FieldValidatorInterface;

abstract class FieldValidator implements FieldValidatorInterface
{

    protected string $error;

    /**
     * @param $name
     * @param $args
     *
     * @return $this
     * @throws \ReflectionException
     *
     * allows us to change error messages
     */
    public function __call($name, $args)
    {
        if (property_exists($this, $name)
            && ($propertyType = (new \ReflectionProperty($this, $name))->getType())
            && ($propertyType->getName() === 'string')) {
            $this->$name = $args[0];
            return $this;
        } else {
            throw new BadMethodCallException(static::class, $name);
        }
    }

    public function getError(): ?string
    {
        return $this->error;
    }

    public function error(string $msg, array $replacements)
    {
        foreach ($replacements as $replacement => $value) {
            if (is_object($value))
                $value = get_class($value);
            elseif(is_array($value))
                $value = json_encode($value);

            $value = (string) $value;
            $replacements["%$replacement%"] = $value;
            unset($replacements[$replacement]);
        }

        $msg = strtr($msg, $replacements);
        $this->error = $msg;
    }

    abstract public function validate($value): bool;
}