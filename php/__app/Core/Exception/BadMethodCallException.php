<?php


namespace Core\Exception;


class BadMethodCallException extends BaseException
{
    protected string $err = "Method \"%s::%s\" does not exists.";

    public function __construct(string $class, string $method, int $code = 0)
    {
        parent::__construct($class, $method);
        $this->setCode($code);
    }
}