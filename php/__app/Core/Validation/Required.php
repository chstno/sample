<?php


namespace Core\Validation;


class Required extends FieldValidator
{

    private string $errorMsg = "Field [%fieldName%] is required.";
    private ?string $fieldName;

    public function __construct(string $fieldName)
    {
        $this->fieldName = $fieldName;
    }

    public function validate($value): bool
    {
        if ($value === null) {
            $this->error($this->errorMsg, ['fieldName' => $this->fieldName]);
            return false;
        }

        return true;
    }
}