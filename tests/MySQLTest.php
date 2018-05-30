<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pontikis\Database\Dacapo;

final class MySQLTest extends TestCase
{
    protected static $db;

    protected static $db_wrong_rdbms;

    protected static $db_wrong_logins;

    protected static $mc;

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class                            //
    //                                                                //
    // With localhost port is ignored. Use 127.0.0.1 instead          //
    ////////////////////////////////////////////////////////////////////
    public static function setUpBeforeClass()
    {
        self::$db = [
            'rdbms'           => 'MYSQLi',
            'db_server'       => $GLOBALS['MYSQL_SERVER'],
            'db_user'         => $GLOBALS['MYSQL_USER'],
            'db_passwd'       => $GLOBALS['MYSQL_PASSWD'],
            'db_name'         => $GLOBALS['MYSQL_DBNAME'],
            // optional
            'db_port'         => $GLOBALS['MYSQL_PORT'],
            'charset'         => $GLOBALS['MYSQL_CHARSET'],
            // to be removed
            'use_pst'         => true,
            'pst_placeholder' => 'question_mark',
        ];

        self::$db_wrong_rdbms          = self::$db;
        self::$db_wrong_rdbms['rdbms'] = 'WRONG_RDBMS';

        self::$db_wrong_logins              = self::$db;
        self::$db_wrong_logins['db_passwd'] = 'WRONG_PASSWORD';

        self::$mc = [
            'mc_pool'       => [
                [
                    'mc_server' => '127.0.0.1',
                    'mc_port'   => '11211',
                    'mc_weight' => 0,
                ],
            ],
            'use_memcached' => true,
        ];
    }

    ////////////////////////////////////////////////////////////////////
    // Tests                                                          //
    ////////////////////////////////////////////////////////////////////
    public function test0()
    {
        $this->assertSame(
            $GLOBALS['MYSQL_PORT'],
            ini_get('mysqli.default_port')
        );
    }

    public function test1()
    {
        $ds = new Dacapo(self::$db, self::$mc);

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
        $this->expectExceptionMessage(dacapo::ERROR_RDBMS_NOT_SUPPORTED);
        $ds = new Dacapo(self::$db_wrong_rdbms, self::$mc);
    }

    public function test3()
    {
        $ds = new Dacapo(self::$db_wrong_logins, self::$mc);
        $this->expectException(mysqli_sql_exception::class);
        $ds->db_connect();
    }

    public function test4()
    {
        $ds            = new Dacapo(self::$db, self::$mc);
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
        $ds            = new Dacapo(self::$db, self::$mc);
        $sql           = 'SELECT * FROM customers_xx';
        $bind_params   = [];
        $query_options = [];
        $this->expectException(mysqli_sql_exception::class);
        $res = $ds->select($sql, $bind_params, $query_options);
    }
}
