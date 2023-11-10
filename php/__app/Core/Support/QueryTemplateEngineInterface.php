<?php


namespace Core\Support;


interface QueryTemplateEngineInterface extends \Stringable
{
    public function select(string ...$select): static;
    public function addSelect(string ...$select): static;
    public function update(string $table): static;
    public function insert(string $table, array $fields): static;
    public function where(string ...$where): static;
    public function from(string $table): static;
    public function join(string $join, string $on, string $type): static;
    public function having(string ...$having): static;
    public function set(string ...$set): static;
    public function limit(int $limit, int $offset = 0): static;
    public function values(string ...$values): static;
    public function order(string ...$order): static;
    public function delete();
    public function union(string $union): static;
    public function clean(): void;
}