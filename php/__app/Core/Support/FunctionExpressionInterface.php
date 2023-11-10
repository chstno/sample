<?php


namespace Core\Support;


interface FunctionExpressionInterface
{
    public function ifnull(string $expr, string $return): static;
    public function avg(string $var): static;
    public function count(string $var): static;
    public function sum(string $var): static;
    public function exists(): static;
    public function if(string $if, string $then, string $otherwise): static;
    public function case(string $case, string $when, string $then): static;
    public function func(string $name, string ...$args): static;
}