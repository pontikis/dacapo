<?php

declare(strict_types=1);

namespace Pontikis\Database;

use ErrorException;

class DacapoErrorException extends ErrorException
{
    private $sql;

    private $bind_params;

    public function getSQL()
    {
        return $this->sql;
    }

    public function setSQL(string $sql)
    {
        $this->sql = $sql;

        return $this;
    }

    public function getBindParams()
    {
        return $this->bind_params;
    }

    public function setBindParams(array $bind_params)
    {
        $this->bind_params = $bind_params;

        return $this;
    }
}
