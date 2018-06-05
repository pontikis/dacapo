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

    protected static $mc;

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class                            //
    ////////////////////////////////////////////////////////////////////
    public static function setUpBeforeClass()
    {
        self::$db_with_server_name = [
            'rdbms'     => 'POSTGRES',
            'db_server' => $GLOBALS['POSTGRES_SERVER_NAME'],
            'db_user'   => $GLOBALS['POSTGRES_USER'],
            'db_passwd' => $GLOBALS['POSTGRES_PASSWD'],
            'db_name'   => $GLOBALS['POSTGRES_DBNAME'],
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

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT']);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);

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

        $ds = new Dacapo(self::$db_with_server_ip, self::$mc);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT']);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );

        $ds = new Dacapo(self::$db_with_server_ip, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);

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
        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT_WRONG']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails09a()
    {
        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT_WRONG']);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails10()
    {
        $ds = new Dacapo(self::$db_with_server_ip, self::$mc);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT_WRONG']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails10a()
    {
        $ds = new Dacapo(self::$db_with_server_ip, self::$mc);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT_WRONG']);
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
        $ds->setPgConnectForceNew(true);
        $sql           = 'SELECT * FROM test.customers_en';
        $bind_params   = [];
        $query_options = [];
        $res           = $ds->select($sql, $bind_params, $query_options);
        $this->assertSame(
            100,
            $ds->getNumRows()
        );

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql           = 'SELECT * FROM customers_en';
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
        $ds->setPgConnectForceNew(true);        
        $sql           = 'SELECT * FROM test.customers_el';
        $bind_params   = [];
        $query_options = [];
        $res           = $ds->select($sql, $bind_params, $query_options);
        $this->assertSame(
            105,
            $ds->getNumRows()
        );

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql           = 'SELECT * FROM customers_el';
        $bind_params   = [];
        $query_options = [];
        $res           = $ds->select($sql, $bind_params, $query_options);
        $this->assertSame(
            105,
            $ds->getNumRows()
        );
    }

    public function testSelect02()
    {
        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $sql           = 'SELECT lastname FROM test.customers_en WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];
        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql           = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];
        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $sql           = 'SELECT lastname FROM test.customers_en WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];
        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET_WRONG']);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql           = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];
        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );
    }

    public function testSelect02el()
    {
        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $sql           = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];
        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];
        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);     
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $sql           = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];
        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);        
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];
        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );   

        $ds            = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);     
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET_WRONG']);
        $sql           = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];

        if ('WIN1256' === $GLOBALS['POSTGRES_CHARSET_WRONG']) {
            $this->expectException(DacapoErrorException::class);
        }

        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertNotEquals(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name, self::$mc);
        $ds->setPgConnectForceNew(true);        
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET_WRONG']);        
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $query_options = ['get_row' => true];

        if ('WIN1256' === $GLOBALS['POSTGRES_CHARSET_WRONG']) {
            $this->expectException(DacapoErrorException::class);
        }

        $ds->select($sql, $bind_params, $query_options);
        $row = $ds->getData();
        $this->assertNotEquals(
            'Γεωργίου',
            $row['lastname']
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
