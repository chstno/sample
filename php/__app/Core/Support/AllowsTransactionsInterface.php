<?php


namespace Core\Support;


interface AllowsTransactionsInterface
{
    public function beginTransaction(): bool;
    public function commit(): bool;
    public function rollback(): bool;
}