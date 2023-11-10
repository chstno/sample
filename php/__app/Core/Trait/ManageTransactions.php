<?php

namespace Core\Trait;

use Core\Database\DBConnectionDriverInterface;

/**
 * Trait ManageTransactions
 *
 * @property DBConnectionDriverInterface $connectionDriver;
 * @package Core\Trait
 */
trait ManageTransactions
{

    public function beginTransaction(): bool
    {
        return $this->connectionDriver->beginTransaction();
    }

    public function commit(): bool
    {
        return $this->connectionDriver->commit();
    }

    public function rollback(): bool
    {
        return $this->connectionDriver->rollback();
    }
}