<?php


namespace Core\Validation;


class Email extends FieldValidator
{

    private string $typeErrorMsg = "Email is not valid.";

    public function validate($value): bool
    {
        if ($value === null)
            return true;

        if (!is_string($value) || !filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->error($this->typeErrorMsg, []);
            return false;
        }

        return true;
    }
}