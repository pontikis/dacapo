<?php

declare(strict_types=1);

use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;
use Pontikis\Database\Dacapo;
use Pontikis\Database\DacapoErrorException;

final class PostgresqlTest extends TestCase
{
    protected static $db_with_server_name;

    protected static $db_with_server_ip;

    protected static $db_wrong_rdbms;

    protected static $db_wrong_server_name;

    protected static $db_wrong_server_ip;

    protected static $db_wrong_user_with_server_name;

    protected static $db_wrong_user_with_server_ip;

    protected static $db_wrong_passwd_with_server_name;

    protected static $db_wrong_passwd_with_server_ip;

    protected static $db_wrong_dbname_with_server_name;

    protected static $db_wrong_dbname_with_server_ip;

    protected static $db_wrong_port_with_server_name;

    protected static $db_wrong_port_with_server_ip;

    protected static $db_wrong_charset_with_server_name;

    protected static $db_wrong_charset_with_server_ip;

    protected static $mc;

    protected static $regex_not_dacapo;

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class                            //
    //                                                                //
    // With localhost port is ignored. Use 127.0.0.1 instead          //
    ////////////////////////////////////////////////////////////////////
    public static function setUpBeforeClass()
    {
        self::$db_with_server_name = [
            'rdbms'           => 'POSTGRES',
            'db_server'       => $GLOBALS['POSTGRES_SERVER_NAME'],
            'db_user'         => $GLOBALS['POSTGRES_USER'],
            'db_passwd'       => $GLOBALS['POSTGRES_PASSWD'],
            'db_name'         => $GLOBALS['POSTGRES_DBNAME'],
            // optional
            'db_port'         => $GLOBALS['POSTGRES_PORT'],
            'charset'         => $GLOBALS['POSTGRES_CHARSET'],
            // to be removed
            'use_pst'         => true,
            'pst_placeholder' => 'question_mark',
        ];

        self::$db_with_server_ip              = self::$db_with_server_name;
        self::$db_with_server_ip['db_server'] = $GLOBALS['POSTGRES_SERVER_IP'];

        self::$db_wrong_rdbms          = self::$db_with_server_name;
        self::$db_wrong_rdbms['rdbms'] = $GLOBALS['RDBMS_WRONG'];

        self::$db_wrong_server_name              = self::$db_with_server_name;
        self::$db_wrong_server_name['db_server'] = $GLOBALS['POSTGRES_SERVER_NAME_WRONG'];

        self::$db_wrong_server_ip              = self::$db_with_server_name;
        self::$db_wrong_server_ip['db_server'] = $GLOBALS['POSTGRES_SERVER_IP_WRONG'];

        self::$db_wrong_user_with_server_name            = self::$db_with_server_name;
        self::$db_wrong_user_with_server_name['db_user'] = $GLOBALS['POSTGRES_USER_WRONG'];

        self::$db_wrong_user_with_server_ip            = self::$db_with_server_ip;
        self::$db_wrong_user_with_server_ip['db_user'] = $GLOBALS['POSTGRES_USER_WRONG'];

        self::$db_wrong_passwd_with_server_name              = self::$db_with_server_name;
        self::$db_wrong_passwd_with_server_name['db_passwd'] = $GLOBALS['POSTGRES_PASSWD_WRONG'];

        self::$db_wrong_passwd_with_server_ip              = self::$db_with_server_ip;
        self::$db_wrong_passwd_with_server_ip['db_passwd'] = $GLOBALS['POSTGRES_PASSWD_WRONG'];

        self::$db_wrong_dbname_with_server_name            = self::$db_with_server_name;
        self::$db_wrong_dbname_with_server_name['db_name'] = $GLOBALS['POSTGRES_DBNAME_WRONG'];

        self::$db_wrong_dbname_with_server_ip            = self::$db_with_server_ip;
        self::$db_wrong_dbname_with_server_ip['db_name'] = $GLOBALS['POSTGRES_DBNAME_WRONG'];

        self::$db_wrong_port_with_server_name            = self::$db_with_server_name;
        self::$db_wrong_port_with_server_name['db_port'] = $GLOBALS['POSTGRES_PORT_WRONG'];

        self::$db_wrong_port_with_server_ip            = self::$db_with_server_ip;
        self::$db_wrong_port_with_server_ip['db_port'] = $GLOBALS['POSTGRES_PORT_WRONG'];

        self::$db_wrong_charset_with_server_name            = self::$db_with_server_name;
        self::$db_wrong_charset_with_server_name['charset'] = $GLOBALS['POSTGRES_CHARSET_WRONG'];

        self::$db_wrong_charset_with_server_ip            = self::$db_with_server_ip;
        self::$db_wrong_charset_with_server_ip['charset'] = $GLOBALS['POSTGRES_CHARSET_WRONG'];

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
    // Test instance                                                  //
    ////////////////////////////////////////////////////////////////////
    public function testInstance01()
    {
        $ds = new Dacapo(self::$db_with_server_name, self::$mc);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );
    }

    public function testInstance02()
    {
        $ds = new Dacapo(self::$db_with_server_ip, self::$mc);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );
    }

    public function testInstanceFails01()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Dacapo::ERROR_RDBMS_NOT_SUPPORTED);
        $ds = new Dacapo(self::$db_wrong_rdbms, self::$mc);
    }

    ////////////////////////////////////////////////////////////////////
    // Test dbConnect()                                               //
    ////////////////////////////////////////////////////////////////////
    public function testConnect01()
    {
        $ds = new Dacapo(self::$db_with_server_name, self::$mc);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );
    }

    public function testConnect02()
    {
        $ds = new Dacapo(self::$db_with_server_ip, self::$mc);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );
    }

    /**
     * This test will take more than three minutes to be executed
     * (pg_connect timeout).
     * To avoid this use POSTGRES_PG_CONNECT_TIMOUT.
     */
    public function testConnectFails01()
    {
        $ds = new Dacapo(self::$db_wrong_server_name, self::$mc);
        $ds->setPgConnectTimout((int) $GLOBALS['POSTGRES_PG_CONNECT_TIMOUT']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails01a()
    {
        $ds = new Dacapo(self::$db_wrong_server_name, self::$mc);
        $ds->setPgConnectTimout((int) $GLOBALS['POSTGRES_PG_CONNECT_TIMOUT']);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    /**
     * This test will take more than three minutes to be executed
     * (pg_connect timeout).
     * To avoid this use POSTGRES_PG_CONNECT_TIMOUT.
     */
    public function testConnectFails02()
    {
        $ds = new Dacapo(self::$db_wrong_server_ip, self::$mc);
        $ds->setPgConnectTimout((int) $GLOBALS['POSTGRES_PG_CONNECT_TIMOUT']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails02a()
    {
        $ds = new Dacapo(self::$db_wrong_server_ip, self::$mc);
        $ds->setPgConnectTimout((int) $GLOBALS['POSTGRES_PG_CONNECT_TIMOUT']);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails03()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_name, self::$mc);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails03a()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_name, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails04()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_ip, self::$mc);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails04a()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_ip, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails05()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_name, self::$mc);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails05a()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_name, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails06()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_ip, self::$mc);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails06a()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_ip, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails07()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_name, self::$mc);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails07a()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_name, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails08()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_ip, self::$mc);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails08a()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_ip, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails09()
    {
        $ds = new Dacapo(self::$db_wrong_port_with_server_name, self::$mc);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails09a()
    {
        $ds = new Dacapo(self::$db_wrong_port_with_server_name, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails10()
    {
        $ds = new Dacapo(self::$db_wrong_port_with_server_ip, self::$mc);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails10a()
    {
        $ds = new Dacapo(self::$db_wrong_port_with_server_ip, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    ////////////////////////////////////////////////////////////////////
    // Test select()                                                  //
    ////////////////////////////////////////////////////////////////////
    public function testSelect01()
    {
        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $sql           = 'SELECT * FROM test.customers_en';
        $bind_params   = [];
        $query_options = [];
        $res           = $ds->select($sql, $bind_params, $query_options);
        $this->assertSame(
            100,
            $ds->getNumRows()
        );
    }

    public function testSelect01el()
    {
        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $sql           = 'SELECT * FROM test.customers_el';
        $bind_params   = [];
        $query_options = [];
        $res           = $ds->select($sql, $bind_params, $query_options);
        $this->assertSame(
            105,
            $ds->getNumRows()
        );

        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $sql           = 'SELECT * FROM customers_el';
        $bind_params   = [];
        $query_options = [];
        $res           = $ds->select($sql, $bind_params, $query_options);
        $this->assertSame(
            105,
            $ds->getNumRows()
        );
    }

    public function testSelectFails01()
    {
        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $sql           = 'SELECT * FROM test.customers_xx';
        $bind_params   = [];
        $query_options = [];
        $this->expectException(DacapoErrorException::class);
        $res = $ds->select($sql, $bind_params, $query_options);
    }

    public function testSelectFails01a()
    {
        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setUseDacapoErrorHandler(false);
        $sql           = 'SELECT * FROM test.customers_xx';
        $bind_params   = [];
        $query_options = [];
        $this->expectException(Warning::class);
        $res = $ds->select($sql, $bind_params, $query_options);
    }
}
