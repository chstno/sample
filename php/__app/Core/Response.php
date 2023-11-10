<?php


namespace Core;


use Core\Support\ResponseInterface;

class Response implements \Stringable, ResponseInterface
{

    protected array     $headers;
    protected string    $body;
    protected int       $status;
    protected string    $version = '1.1';
    protected string    $statusText;

    protected array $statusTexts = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        103 => 'Early Hints',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Range Not Satisfiable',
        417 => 'Expectation Failed',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        511 => 'Network Authentication Required',
    ];


    public function construct()
    {
        if ($args = func_get_args()) {
            $this->make(...$args);
        }
    }


    public function make(string $body = '', int $status = 200, array $headers = []): static
    {
        $this->setBody($body);
        $this->setStatusCode($status);
        $this->loadHeadersList();
        $this->setHeaders($headers);

        return $this;
    }

    public function loadHeadersList(): static
    {
        // all uses of header() or working with cookies/session
        $this->headers = [];
        foreach (headers_list() as $header) {
            $header = explode(':', $header);
            $headerName = trim($header[0], ' ');
            $headerValue = trim($header[1], ' ');
            $this->header($headerName, $headerValue);
        }

        return $this;
    }

    public function __toString(): string
    {
        $content = sprintf('HTTP/%s %s %s', $this->version, $this->status, $this->statusText)."\r\n";
        $headers = '';

        foreach ($this->headers as $header => $values) {
            foreach ($values as $value)
                $headers .= "{$header}: $value\r\n";
        }

        $content .= "{$headers}{$this->body}";

        return $content;
    }

    public function setStatusCode(int $status): static
    {
        if ($status < 100 || $status > 599) {
            throw new \InvalidArgumentException("[". static::class ."]: invalid status code, must be in interval [100-599]");
        }

        $this->status = $status;
        $this->statusText = $this->statusTexts[$status] ?? 'unknown';

        return $this;
    }

    protected function header(string $key, string|array $value): static
    {
        $key = ucwords(strtolower($key), '-');
        $value = is_string($value) ? [$value] : $value;

        if (!isset($this->headers[$key])) {
            $this->headers[$key] = $value;
        } else {
            $this->headers[$key] = array_merge($this->headers[$key], $value);
        }

        return $this;
    }

    public function setHeaders(array $headers): static
    {
        foreach ($headers as $header => $values) {
            $this->header($header, $values);
        }

        return $this;
    }

    public function setBody(string $body): static
    {
        $this->body = $body;
        return $this;
    }

    public function send(bool $force = false): static
    {

        $this->sendHeaders();
        $this->sendBody();

        if ($force) {
            if (\function_exists('fastcgi_finish_request'))
                fastcgi_finish_request();
            else
                $this->closeBuffers();// yeah, with flush, it happens automatically (by default)
        }

        return $this;
    }

    public function sendHeaders(): static
    {
        if (headers_sent()) {
            return $this;
        }

        foreach ($this->headers as $header => $values)
        {
            $replace = $header === 'Content-Type';
            foreach ($values as $value) {
                header("$header: $value", $replace, $this->status);
            }
        }

        header(sprintf('HTTP/%s %s %s', $this->version, $this->status, $this->statusText));

        return $this;
    }

    public function sendBody(): static
    {
        echo $this->body;
        return $this;
    }

    public function closeBuffers(bool $flush = true, ?int $toLevel = 0): void
    {
        $bufferLevel = ob_get_level();

        while ($bufferLevel-- > $toLevel) {
            if ($flush)
                ob_end_flush();
            else
                ob_end_clean();
        }
    }

}