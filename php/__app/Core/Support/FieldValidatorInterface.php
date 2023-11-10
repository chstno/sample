<?php


namespace Core\Support;


interface FieldValidatorInterface
{
    public function validate($value): bool;
    public function getError(): ?string;
}