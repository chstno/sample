<?php


namespace Core\Support;


interface SessionStorageInterface extends \ArrayAccess
{
    public function get(string $key);
    public function set(string $key, $value): void;
    public function isset(string $key): bool;
    public function all(): array;
    public function delete(string $key);
    public function abort(): bool;
    public function destroy(): bool;
}