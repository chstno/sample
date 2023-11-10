<?php


namespace Core\Support;


interface RequestInterface
{
    public function getUri(): string;
    public function query(string|null $key): mixed;
    public function post(string|null $key): mixed;
    public function files(string|null $key): array;
    public function getPath(): string;
    public function getMethod(): string;
    public function getBody(): string;
}