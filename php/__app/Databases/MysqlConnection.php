<?php

namespace Databases;

use Core\Database\DBConnection;
use Core\Database\PDOConnectionDriver;
use Core\Support\AllowsTransactionsInterface;
use Core\Trait\ManageTransactions;

class MysqlConnection extends DBConnection implements AllowsTransactionsInterface
{
    use ManageTransactions;

    public function __construct(PDOConnectionDriver $connectionDriver)
    {
        parent::__construct($connectionDriver);
        $this->connect($this->conf);
    }
}