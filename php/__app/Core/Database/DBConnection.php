<?php


namespace Core\Database;


use Core\Service\ReflectionCaller;
use Core\Support\AllowsTransactionsInterface;

/**
 * Class DBConnection
 ** @package Core\Database
 */

abstract class DBConnection
{
    /**
     * @var DBConnectionDriverInterface
     */
    protected DBConnectionDriverInterface $connectionDriver;
    protected array $conf;

    public function __construct(DBConnectionDriverInterface $connectionDriver)
    {
        $this->connectionDriver = $connectionDriver;
        $this->setDefaultConf();
    }

    protected function setDefaultConf()
    {
        $this->conf = \app()->dbConf[static::class][$this->connectionDriver::class] ?? [];
    }

    /**
     * @throws \BadFunctionCallException
     */
    public function connect(array $conf)
    {
        return ReflectionCaller::callFunctionWithNamedParams([$this->connectionDriver, 'connect'], $conf);
    }

    public function prepare(string $query): void
    {
        $this->connectionDriver->prepare($query);
    }

    public function bindParams(array $params): void
    {
        $this->connectionDriver->bindParams($params);
    }

    public function execute(): bool
    {
        return $this->connectionDriver->execute();
    }

    public function disconnect()
    {
        return $this->connectionDriver->disconnect();
    }

    public function fetch(): iterable
    {
        return $this->connectionDriver->fetch();
    }

    public function fetchAll(): iterable
    {
        return $this->connectionDriver->fetchAll();
    }

    public function query(string $query = '', array $params = [], $fetchMode = FetchTypes::FETCH_ALL)
    {
        if ($query) $this->connectionDriver->prepare($query); // if we want to re-run the same query with different params
        if ($params) $this->connectionDriver->bindParams($params);
        if ($result = $this->connectionDriver->execute() ?? [])
            $result = match ($fetchMode) {
                FetchTypes::FETCH_ALL       => $this->connectionDriver->fetchAll(),
                FetchTypes::FETCH           => $this->connectionDriver->fetch(),
                FetchTypes::AFFECTED        => $this->connectionDriver->affectedRows(),
                FetchTypes::LAST_INSERT_ID  =>
                $this->connectionDriver->affectedRows() ?
                    $this->connectionDriver->lastInsertId() : 0,
            };

        return $result;
    }

    public function lastInsertId(): int
    {
        return $this->connectionDriver->lastInsertId();
    }

    public function affectedRows(): int
    {
        return $this->connectionDriver->affectedRows();
    }

    public function database(): string
    {
        return $this->connectionDriver->database();
    }
}