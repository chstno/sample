<?php


namespace Core\Validation;


use http\Exception\InvalidArgumentException;

class Str extends FieldValidator
{

    private ?int $max;
    private ?int $min;

    /**
     * @var string|null - pcre pattern
     */
    private ?string $pattern;

    /**
     * @var string|null - for errors
     */
    private ?string $userFriendlyPattern;

    private string $typeErrorMsg = "%value% is not valid. String expected, %type% - given.";
    private string $minErrorMsg = "String is not valid. It should contain at least %min% characters.";
    private string $maxErrorMsg = "String is not valid. The length should not exceed %max% characters.";
    private string $patternErrorMsg = "String does not match expected pattern: %pattern%";

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    public function validate($value): bool
    {
        if ($value === null)
            return true;

        if (!is_string($value)) {
            $this->error($this->typeErrorMsg, ['type' => gettype($value)]);
            return false;
        }

        if ($this->min && mb_strlen($value) < $this->min) {
            $this->error($this->minErrorMsg, ['min' => $this->min]);
            return false;
        }

        if ($this->max && mb_strlen($value) > $this->max) {
            $this->error($this->maxErrorMsg, ['max' => $this->max]);
            return false;
        }

        if ($this->pattern && !preg_match($this->pattern, $value)) {
            $this->error($this->patternErrorMsg, ['pattern' => $this->userFriendlyPattern]);
            return false;
        }

        return true;
    }

    public function pattern(string $pattern, string $userFriendlyPattern)
    {
        try {
            preg_match($pattern, "...");
        } catch(\ErrorException $e) {
            throw new \InvalidArgumentException(sprintf("[%s]: Invalid pcre-pattern given: %s", static::class, $pattern));
        }

        $this->pattern = $pattern;
        $this->userFriendlyPattern = $userFriendlyPattern;
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