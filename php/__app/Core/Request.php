<?php


namespace Core;


use Core\Support\RequestInterface;

class Request implements RequestInterface
{

    protected string $uri;
    protected string $method;
    protected string $path;
    protected string $body;

    protected array $parsedUri;
    protected array $query;
    protected array $post;
    protected array $files;
    protected array $cookies;


    public function __construct(
        string  $uri = '',
        string  $method = '',
        array   $query = [],
        array   $post = [],
        string  $body = '',
        array   $files = [],
        array   $cookies = [])
    {
        if (!func_get_args())
            $this->initFromGlobals();
        else
            $this->init(...func_get_args());
    }

    public function init(
        string  $uri,
        string  $method,
        array   $query = [],
        array   $post = [],
        string  $body = '',
        array   $files = [],
        array   $cookies = [])
    {
        $this->uri = $uri;
        $this->method = mb_strtoupper($method);
        $this->query = $query;
        $this->post = $post;
        $this->body = $body;
        $this->files = $files;
        $this->cookies = $cookies;
        $this->parsedUri = parse_url($this->uri) ?? [];
        $this->path = $this->parsedUri['path'];
    }

    protected function initFromGlobals(): void
    {
        $body = file_get_contents('php://input');
        $this->init($_SERVER['REQUEST_URI'], $_SERVER['REQUEST_METHOD'], $_GET, $_POST, $body, $_FILES, $_COOKIE);
    }

    public function getUri(): string
    {
        return $this->uri;
    }

    public function query(string|null $key): mixed
    {
        return $key ? $this->query[$key] ?? [] : $this->query;
    }

    public function post(string|null $key): mixed
    {
        return $key ? $this->post[$key] ?? [] : $this->post;
    }

    public function files(string|null $key): array
    {
        return $key ? $this->files[$key] ?? [] : $this->files;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getBody(): string
    {
        return $this->body;
    }
}