<?php


namespace Core\Validation;


class Integer extends FieldValidator
{

    private ?int $min;
    private ?int $max;

    private string $minErrorMsg = "%value% is not valid. It must be bigger than %min%.";
    private string $maxErrorMsg = "%value% is not valid. It must be smaller than %max%.";
    private string $typeErrorMsg = "%value% is not valid. Integer expected, %type% - given.";


    public function validate($value): bool
    {
        if ($value === null)
            return true;

        if (!ctype_digit(strval($value))) {
            $this->error($this->typeErrorMsg, ['type' => gettype($value)]);
            return false;
        }

        if ($value < $this->min) {
            $this->error($this->minErrorMsg, ['value' => $value, 'min' => $this->min]);
            return false;
        }

        if ($value > $this->max) {
            $this->error($this->maxErrorMsg, ['value' => $value, 'min' => $this->max]);
            return false;
        }

        return true;
    }

    public function min(int $min): static
    {
        $this->min = $min;
        return $this;
    }

    public function max(int $max): static
    {
        $this->max = $max;
        return $this;
    }
}