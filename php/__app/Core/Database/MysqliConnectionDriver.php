<?php


namespace Core\Database;


use Core\Support\AllowsTransactionsInterface;
use mysqli;
use mysqli_result;

class MysqliConnectionDriver implements DBConnectionDriverInterface, AllowsTransactionsInterface
{

    const FETCH_MODE = \MYSQLI_ASSOC;

    protected mysqli|null $mysqli;

    protected \mysqli_stmt|null $stmt;

    protected mysqli_result|false $result;

    protected array $params = [];
    protected string $sql = '';
    protected string $db = '';


    public function connect(string $ext, string $host, string $user, string $pass, string $db, null|int $port)
    {
        $this->disconnect();
        try {
            $this->mysqli = new mysqli($host, $user, $pass, $db, $port);
            $this->db = $db;
        } catch (\mysqli_sql_exception $e) {
            throw $e;
        }
    }

    public function __destruct()
    {
        $this->disconnect();
    }

    public function setReport(): void
    {
        mysqli_report(MYSQLI_REPORT_STRICT | MYSQLI_REPORT_ALL);
    }

    /**
     * obviously, that's not the best place for resolving this
     * but, i think it acceptable, we just assume that all drivers
     * can work with named parameters (+ usability, and consistency)
     *
     * todo: refactor
     */
    protected function replaceNamedParams(): void
    {
        preg_match_all('/\:([a-z\_]+)/i', $this->sql, $params);
        if (isset($params[1]))
            $this->params = $params[1]; // save the sequence

        $this->sql = (string) preg_replace('/\:[a-z\_]+/i', '?', $this->sql);
    }

    public function prepare(string $query): void
    {
        $this->sql = $query;
        $this->params = [];
        $this->replaceNamedParams();
        $this->stmt = $this->mysqli->stmt_init();
        $this->stmt->prepare($this->sql);
    }

    public function bindParams(array $params): void
    {
        $cnt = 0;
        $new = [];
        $types = '';

        if ($this->params) {
            foreach ($this->params as $param) {
                $name = "param{$cnt}";
                $$name = $params[$param];
                $new[] = &$$name;
                $types .= is_int($params[$param]) ? 'i' : 's';
                $cnt++;
            }
        } else {
            foreach ($params as $param) {
                $name = "param{$cnt}";
                $$name = $param;
                $new[] = &$$name;
                $types .= is_int($param) ? 'i' : 's';
                $cnt++;
            }
        }

        $this->stmt->bind_param($types, ...$new);
    }

    public function execute(): bool
    {
        $exec = $this->stmt->execute();
        $this->result = $this->stmt->get_result();
        return $exec;
    }

    public function fetch(): array
    {
        return $this->result->fetch_assoc();
    }

    public function fetchAll(): array
    {
        return $this->result->fetch_all(static::FETCH_MODE);
    }

    public function disconnect()
    {
        if ($this->mysqli)
            $this->mysqli->close();
        $this->mysqli = null;
    }

    public function lastInsertId(): int
    {
        return $this->mysqli->insert_id;
    }

    public function quote(string $str): string
    {
        return mysqli_real_escape_string($this->mysqli, $str);
    }

    public function affectedRows(): int
    {
        if ($this->mysqli)
            return $this->mysqli->affected_rows;
        else
            return 0;
    }

    // todo: make general abstract parent for drivers // or removing duplicates for such methods
    public function database(): string
    {
        return $this->db;
    }

    public function beginTransaction(): bool
    {
        return $this->mysqli->begin_transaction();
    }

    public function commit(): bool
    {
        return $this->mysqli->commit();
    }

    public function rollback(): bool
    {
        return $this->mysqli->rollback();
    }
}