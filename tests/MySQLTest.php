<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class MySQLTest extends TestCase
{
    private $db = [
        'rdbms'           => 'MYSQLi',
        'db_server'       => 'localhost',
        'db_user'         => 'testdb',
        'db_passwd'       => 'testdb',
        'db_name'         => 'testdb',
        // optional
        'db_port'         => 105,
        'charset'         => 'utf8mb4',
        // to be removed
        'use_pst'         => true,
        'pst_placeholder' => 'question_mark',
    ];

    private $db_wrong_rdbms = [
        'rdbms'           => 'MySQLi',
        'db_server'       => 'localhost',
        'db_user'         => 'testdb',
        'db_passwd'       => 'testdb',
        'db_name'         => 'testdb',
        'db_port'         => '3309',
        'charset'         => 'utf8',
        'use_pst'         => true,
        'pst_placeholder' => 'question_mark',
    ];

    private $db_wrong_logins = [
        'rdbms'           => 'MYSQLi',
        'db_server'       => 'localhost',
        'db_user'         => 'testdb1',
        'db_passwd'       => 'testdb',
        'db_name'         => 'testdb',
        'db_port'         => '3309',
        'charset'         => 'utf8',
        'use_pst'         => true,
        'pst_placeholder' => 'question_mark',
    ];

    private $mc = [
        'mc_pool'       => [
            [
                'mc_server' => '127.0.0.1',
                'mc_port'   => '11211',
                'mc_weight' => 0,
            ],
        ],
        'use_memcached' => true,
    ];

    ////////////////////////////////////////////////////////////////////
    // Tests for isDateTimeStringInDST()                              //
    ////////////////////////////////////////////////////////////////////
    public function test0()
    {
        $this->assertSame(
            3306,
            (int) ini_get('mysqli.default_port')
        );
    }

    public function test1()
    {
        $ds = new dacapo($this->db, $this->mc);

        $this->assertInstanceOf(
            dacapo::class,
            $ds
        );

        $this->assertInstanceOf(
            mysqli::class,
            $ds->db_connect()
        );
    }

    public function test2()
    {
        $this->expectException(Exception::class);
        $ds = new dacapo($this->db_wrong_rdbms, $this->mc);
    }

    public function test3()
    {
        $ds = new dacapo($this->db_wrong_logins, $this->mc);
        $this->expectException(mysqli_sql_exception::class);
        $ds->db_connect();
    }

    public function test4()
    {
        $ds            = new dacapo($this->db, $this->mc);
        $sql           = 'SELECT * FROM customers_en';
        $bind_params   = [];
        $query_options = [];
        $res           = $ds->select($sql, $bind_params, $query_options);
        $this->assertSame(
            100,
            $ds->getNumRows()
        );
    }

    public function test5()
    {
        $ds            = new dacapo($this->db, $this->mc);
        $sql           = 'SELECT * FROM customers_xx';
        $bind_params   = [];
        $query_options = [];
        $this->expectException(mysqli_sql_exception::class);
        $res           = $ds->select($sql, $bind_params, $query_options);
    }
}
