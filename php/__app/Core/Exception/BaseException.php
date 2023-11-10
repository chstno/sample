<?php


namespace Core\Exception;


class BaseException extends \Exception
{

    protected string $err = "";

    public function __construct(...$args)
    {
        if ($this->err) {
            $argsCount = substr_count($this->err, '%s');
            $replaceArgs = array_slice($args, 0, $argsCount);
            $parentArgs = array_slice($args, $argsCount);
            $this->err = sprintf($this->err, ...$replaceArgs);
            parent::__construct($this->err, ...$parentArgs);
        } else {
            parent::__construct(...$args);
        }
    }

    public function setCode($code = 0): static
    {
        $this->code = $code;
        return $this;
    }

    public function setErrorMessage(string $err)
    {
        $this->err = $err;
    }
}