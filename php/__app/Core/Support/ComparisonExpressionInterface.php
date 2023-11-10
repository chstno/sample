<?php


namespace Core\Support;


interface ComparisonExpressionInterface
{
    public function biggerOrEqual(string $var, string $value): static;
    public function bigger(string $var, string $value): static;
    public function lower(string $var, string $value): static;
    public function lowerOrEqual(string $var, string $value): static;
    public function like(string $var, string $value): static;
    public function equal(string $var, string $value): static;
    public function notEqual(string $var, string $value): static;
    public function between(string $var, string|int $val1, string|int $val2): static;
    public function in(string|array $in): static;
}