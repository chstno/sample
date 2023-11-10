<?php


namespace Core\Service;


use Core\Support\FieldValidatorInterface;

class Validator
{
    /**
     * @var array<FieldValidatorInterface>
     */
    private array $validators;

    /**
     * @var array<string,string>
     */
    private array $errors = [];

    public function __construct(array $validationRules)
    {
        foreach ($validationRules as $attr => $validators) {

            if (!is_array($validators))
                $validators = [$validators];

            $this->addFieldValidators($attr, $validators);
        }
    }

    public function validate(array $data): bool
    {
        foreach ($this->validators as $attr => $validators) {

            if (!isset($data[$attr]))
                $data[$attr] = null;

            foreach ($validators as $validator) {
                if ($validator->validate($data[$attr]) === false) {
                    $this->error($attr, (string) $validator->getErrors());
                }
            }
        }

        return empty($this->errors);
    }

    public function getErrors(): array
    {
        return $this->errors;
    }

    private function error(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * @param string $field
     * @param array<FieldValidatorInterface> $validators
     * @return void
     */
    private function addFieldValidators(string $field, array $validators): void
    {
        foreach ($validators as $validator) {
            if (!$validator instanceof FieldValidatorInterface) {
                throw new \InvalidArgumentException(
                    sprintf("{$field} validator must be an instance of FieldValidatorInterface, \"%s\" given.",
                           is_object($validator) ? get_class($validator) : gettype($validator)
                    ));
            }

            if (method_exists($validator, 'init')) {
                $validator->init();
            }

            $this->validators[$field][] = $validator;
        }
    }
}