<?php

namespace Core\Database;

/**
 * Interface DBConnectionDriverInterface
 *
 * @package Core\Database
 */
interface DBConnectionDriverInterface
{
    public function connect(string $ext, string $host, string $user, string $pass, string $db, int $port);
    public function disconnect();
    public function prepare(string $query);
    public function bindParams(array $params);
    public function execute(): bool;
    public function fetch(): iterable;
    public function fetchAll(): iterable;
    public function lastInsertId(): int;
    public function quote(string $str): string;
    public function affectedRows(): int;
    public function database(): string;
}