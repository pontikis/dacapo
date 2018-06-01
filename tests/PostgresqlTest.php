<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pontikis\Database\Dacapo;

final class MySQLTest extends TestCase
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
    public function testInstance1()
    {
        $ds = new Dacapo(self::$db_with_server_name, self::$mc);

        $this->assertInstanceOf(
            dacapo::class,
            $ds
        );
    }

    public function testInstance2()
    {
        $ds = new Dacapo(self::$db_with_server_ip, self::$mc);

        $this->assertInstanceOf(
            dacapo::class,
            $ds
        );
    }

    public function testInstanceFails1()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(dacapo::ERROR_RDBMS_NOT_SUPPORTED);
        $ds = new Dacapo(self::$db_wrong_rdbms, self::$mc);
    }

    ////////////////////////////////////////////////////////////////////
    // Test dbConnect()                                               //
    ////////////////////////////////////////////////////////////////////
    public function testConnect1()
    {
        $ds = new Dacapo(self::$db_with_server_name, self::$mc);

        $this->assertInstanceOf(
            dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );
    }

    public function testConnect2()
    {
        $ds = new Dacapo(self::$db_with_server_ip, self::$mc);

        $this->assertInstanceOf(
            dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );
    }

    /**
     * This test will take more than a minute to be executed
     * (mysqli connection timeout).
     * To avoid this use --enforce-time-limit.
     *
     * @small
     */
    public function testConnectFails1()
    {
        $ds = new Dacapo(self::$db_wrong_server_name, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    /**
     * This test will take more than a minute to be executed
     * (mysqli connection timeout).
     * To avoid this use --enforce-time-limit.
     *
     * @small
     */
    public function testConnectFails2()
    {
        $ds = new Dacapo(self::$db_wrong_server_ip, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    public function testConnectFails3()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_name, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    public function testConnectFails4()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_ip, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    public function testConnectFails5()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_name, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    public function testConnectFails6()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_ip, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    public function testConnectFails7()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_name, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    public function testConnectFails8()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_ip, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    public function testConnectFails9()
    {
        /*        if ('localhost' === $GLOBALS['POSTGRES_SERVER_NAME']) {
                    $this->markTestSkipped(
                      'Using localhost, port is ignored.'
                    );
                }*/
        $ds = new Dacapo(self::$db_wrong_port_with_server_name, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    public function testConnectFails10()
    {
        $ds = new Dacapo(self::$db_wrong_port_with_server_ip, self::$mc);
        $this->expectException(Exception::class);
        $ds->dbConnect();
    }

    ////////////////////////////////////////////////////////////////////
    // Test select()                                                  //
    ////////////////////////////////////////////////////////////////////
    public function test4()
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

    public function test5()
    {
        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $sql           = 'SELECT * FROM test.customers_xx';
        $bind_params   = [];
        $query_options = [];
        $this->expectException(Exception::class);
        $res = $ds->select($sql, $bind_params, $query_options);
    }
}
