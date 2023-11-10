<?php


namespace Core\Support;


interface QueryBuilderInterface extends \Stringable
{
    public function select(string ...$select): static;
    public function addSelect(string ...$select): static;
    public function update(string $table): static;
    public function insert(string $table, array $fields): static;
    public function limit(int $limit, int $offset = 0): static;
    public function from(string $table, string $alias = null);
    public function order(string ...$order): static;
    public function leftJoin(string $joinTable, string|array $on): static;
    public function rightJoin(string $joinTable, string|array $on): static;
    public function innerJoin(string $joinTable, string|array $on): static;
    public function join(string $joinTable, string|array $on, string $type = 'LEFT'): static;
    public function where(array|string $where, string $operator = '&', string $operation = '='): static;
    public function having(array|string $having, string $operator = '&', string $operation = '='): static;
    public function orWhere(array|string $where, string $operator = '&', string $operation = '='): static;
    public function andWhere(array|string $where, string $operator = '&', string $operation = '='): static;
    public function values(string $values): static;
    public function set(array $set): static;
    public function union(string $union): QueryBuilderInterface;
    public function delete();
    public function clean(): void;
}