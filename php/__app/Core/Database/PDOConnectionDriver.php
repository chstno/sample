<?php


namespace Core\Database;


use Core\Support\AllowsTransactionsInterface;
use PDOStatement;

class PDOConnectionDriver implements DBConnectionDriverInterface, AllowsTransactionsInterface
{

    public const FETCH_MODE = \PDO::FETCH_ASSOC;

    protected \PDO|null         $pdo;
    protected PDOStatement|null $prepared;

    protected string    $sql    = '';
    protected array     $params = [];
    protected string    $db     = '';


   public function connect(string $ext, string $host, string $user, string $pass, string $db, null|int $port): bool
   {
       $this->disconnect();
       $port = $port ? "port=$port;" : '';
       $dsn = "{$ext}:host={$host};{$port}dbname={$db};charset=UTF8";

       try {
           $this->pdo = new \PDO($dsn, $user, $pass);
           $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
           $this->db = $db;

       } catch (\PDOException $e) {
           /*return false;*/
           throw $e;
       }

       return true;
   }

    public function prepare(string $query): void
    {
        $this->sql = $query;
        $this->params = [];
        $this->prepared = $this->pdo->prepare($this->sql);
    }

    public function bindParams(array $params): void
    {
       $this->params = $params;
    }

    public function execute(): bool
    {
        return $this->prepared->execute($this->params);
    }

    public function fetch(): array
    {
       return $this->prepared->fetch(static::FETCH_MODE);
    }

    public function fetchAll(): array
    {
        return $this->prepared->fetchAll(static::FETCH_MODE);
    }

    public function disconnect()
    {
        $this->pdo = null;
    }

    public function lastInsertId(): int
    {
        return $this->pdo->lastInsertId();
    }

    public function quote(string $str): string
    {
        return $this->pdo->quote($str);
    }

    public function affectedRows(): int
    {
        if ($this->prepared)
            return $this->prepared->rowCount();
        else
            return 0;
    }

    public function database(): string
    {
        return $this->db;
    }

    public function beginTransaction(): bool
    {
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->pdo->commit();
    }

    public function rollBack(): bool
    {
        return $this->pdo->rollBack();
    }
}