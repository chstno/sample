<?php


namespace Core\Validation;
;

class Numeric extends FieldValidator
{

    private string $typeErrorMsg = "Value expected to be numeric. %value% is given.";

    public function validate($value): bool
    {
        if ($value === null)
            return true;

        if (!is_numeric($value)) {
            $this->error($this->typeErrorMsg, ['value' => $value]);
            return false;
        }

        return true;
    }
}