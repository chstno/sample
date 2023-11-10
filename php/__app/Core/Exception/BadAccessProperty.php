<?php


namespace Core\Exception;


class BadAccessProperty extends BaseException
{
    protected string $err = "[%s]: property {%s} is not accessible!";

    public function __construct(string $class, string $property, int $code = 0)
    {
        parent::__construct($class, $property);
        $this->setCode($code);
    }
}