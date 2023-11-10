<?php


namespace Core\Support;


//todo: separate to different interfaces with field/value/parameter?

interface ExpressionInterface extends \Stringable
{
    public function and(string ...$expr): static;
    public function or(string ...$expr): static;
    public function not(): static;
    public function comma(): static;
    public function sub(self $expression): static;
    public function literal(mixed $value): static;
    public function parameter(string $name): static;
    public function field(string $name, ?string $table): static;
    public function alias(string $alias): static;
    public function table(string $name): static;
}