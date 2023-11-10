<?php


namespace Core\Service;


use Core\Support\SessionStorageInterface;


final class SessionStorage implements SessionStorageInterface
{

    private array $session;

    /**
     * @throws \Exception
     */
    public function __construct(array $options = [])
    {
        switch (session_status()) {
            case PHP_SESSION_DISABLED:
                throw new \Exception("[". self::class ."]: sessions are disabled!");
            case PHP_SESSION_NONE:
                if (!session_start($options))
                    throw new \Exception("[". self::class ."]: failed to start the session!");
                break;
            default:
                break;
        }

        $this->session = &$_SESSION;
    }

    public function abort(): bool
    {
        return session_abort(); // analog of session_write_close();
    }

    public function destroy(): bool
    {
        return session_destroy();
    }

    public function get(string $key)
    {
        return $this->session[$key] ?? null;
    }

    public function set(string $key, $value): void
    {
        $this->session[$key] = $value;
    }

    public function delete(string $key)
    {
        unset($this->session[$key]);
    }

    public function all(): array
    {
        return $this->session;
    }

    public function isset(string $key): bool
    {
        return isset($this->session[$key]);
    }

    // save working as array
    public function offsetExists($offset): bool
    {
        return $this->isset($offset);
    }

    public function offsetGet($offset)
    {
        return $this->get($offset);
    }

    public function offsetSet($offset, $value)
    {
        $this->set($offset, $value);
    }

    public function offsetUnset($offset)
    {
        $this->delete($offset);
    }
}