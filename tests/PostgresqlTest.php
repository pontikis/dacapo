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

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class                            //
    ////////////////////////////////////////////////////////////////////
    public static function setUpBeforeClass()
    {
        self::$db_with_server_name = [
            'rdbms'     => Dacapo::RDBMS_POSTGRES,
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
    }

    ////////////////////////////////////////////////////////////////////
    // Test instance                                                  //
    ////////////////////////////////////////////////////////////////////
    public function testInstance01()
    {
        $ds = new Dacapo(self::$db_with_server_name);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );
    }

    public function testInstance02()
    {
        $ds = new Dacapo(self::$db_with_server_ip);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );
    }

    public function testInstanceFails01()
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage(Dacapo::ERROR_RDBMS_NOT_SUPPORTED);
        $ds = new Dacapo(self::$db_wrong_rdbms);
    }

    ////////////////////////////////////////////////////////////////////
    // Test dbConnect()                                               //
    ////////////////////////////////////////////////////////////////////
    public function testConnect01()
    {
        $ds = new Dacapo(self::$db_with_server_name);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT']);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );

        $ds = new Dacapo(self::$db_with_server_name);
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
        $ds = new Dacapo(self::$db_with_server_ip);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );

        $ds = new Dacapo(self::$db_with_server_ip);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT']);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertSame(
            'pgsql link',
            get_resource_type($ds->dbConnect())
        );

        $ds = new Dacapo(self::$db_with_server_ip);
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
        $ds = new Dacapo(self::$db_wrong_server_name);
        $ds->setPgConnectTimout((int) $GLOBALS['POSTGRES_PG_CONNECT_TIMOUT']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails01a()
    {
        $ds = new Dacapo(self::$db_wrong_server_name);
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
        $ds = new Dacapo(self::$db_wrong_server_ip);
        $ds->setPgConnectTimout((int) $GLOBALS['POSTGRES_PG_CONNECT_TIMOUT']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails02a()
    {
        $ds = new Dacapo(self::$db_wrong_server_ip);
        $ds->setPgConnectTimout((int) $GLOBALS['POSTGRES_PG_CONNECT_TIMOUT']);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails03()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_name);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails03a()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_name);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails04()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_ip);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails04a()
    {
        $ds = new Dacapo(self::$db_wrong_user_with_server_ip);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails05()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_name);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails05a()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_name);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails06()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_ip);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails06a()
    {
        $ds = new Dacapo(self::$db_wrong_passwd_with_server_ip);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails07()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_name);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails07a()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_name);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails08()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_ip);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails08a()
    {
        $ds = new Dacapo(self::$db_wrong_dbname_with_server_ip);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails09()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT_WRONG']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails09a()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT_WRONG']);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails10()
    {
        $ds = new Dacapo(self::$db_with_server_ip);
        $ds->setDbPort((int) $GLOBALS['POSTGRES_PORT_WRONG']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails10a()
    {
        $ds = new Dacapo(self::$db_with_server_ip);
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
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT * FROM test.customers_en';
        $bind_params = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            100,
            $ds->getNumRows()
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT * FROM customers_en';
        $bind_params = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            100,
            $ds->getNumRows()
        );
    }

    public function testSelect01el()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT * FROM test.customers_el';
        $bind_params = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            105,
            $ds->getNumRows()
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT * FROM customers_el';
        $bind_params = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            105,
            $ds->getNumRows()
        );
    }

    public function testSelect02()
    {
        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT lastname FROM test.customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $sql         = 'SELECT lastname FROM test.customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET_WRONG']);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );
    }

    public function testSelect02el()
    {
        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $sql         = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET_WRONG']);
        $sql         = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params = [1];

        if ('WIN1256' === $GLOBALS['POSTGRES_CHARSET_WRONG']) {
            $this->expectException(DacapoErrorException::class);
        }

        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertNotEquals(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);

        $ds->setPgConnectForceNew(true);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET_WRONG']);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params = [1];

        if ('WIN1256' === $GLOBALS['POSTGRES_CHARSET_WRONG']) {
            $this->expectException(DacapoErrorException::class);
        }

        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertNotEquals(
            'Γεωργίου',
            $row['lastname']
        );
    }

    public function testSelect03()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeAssoc();
        $sql         = 'SELECT lastname FROM test.customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeAssoc();
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeNum();
        $sql         = 'SELECT lastname FROM test.customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row[0]
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeNum();
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row[0]
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeBoth();
        $sql         = 'SELECT lastname FROM test.customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );
        $this->assertSame(
            'Robertson',
            $row[0]
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeBoth();
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );
        $this->assertSame(
            'Robertson',
            $row[0]
        );
    }

    public function testSelect03el()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeAssoc();
        $sql         = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeAssoc();
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeNum();
        $sql         = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row[0]
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeNum();
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row[0]
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeBoth();
        $sql         = 'SELECT lastname FROM test.customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );
        $this->assertSame(
            'Γεωργίου',
            $row[0]
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);

        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setFetchTypeBoth();
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
        $sql         = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getRow();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );
        $this->assertSame(
            'Γεωργίου',
            $row[0]
        );
    }

    public function testSelectFails01()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT * FROM test.customers_xx';
        $bind_params = [];
        $this->expectException(DacapoErrorException::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails01a()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $ds->setUseDacapoErrorHandler(false);
        $sql         = 'SELECT * FROM test.customers_xx';
        $bind_params = [];
        $this->expectException(Warning::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails02()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT * FROM test.customers_en WHERE wrong_column=?';
        $bind_params = [1];
        $this->expectException(DacapoErrorException::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails02a()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $ds->setUseDacapoErrorHandler(false);
        $sql         = 'SELECT * FROM test.customers_en WHERE wrong_column=?';
        $bind_params = [1];
        $this->expectException(Warning::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails03()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT * FROM test.customers_en WHERE id=?';
        $bind_params = [1, 2];
        $this->expectException(DacapoErrorException::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails03a()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $ds->setUseDacapoErrorHandler(false);
        $sql         = 'SELECT * FROM test.customers_en WHERE id=?';
        $bind_params = [1, 2];
        $this->expectException(Warning::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails04()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT * FROM test.customers_en WHERE id=? AND lastname=?';
        $bind_params = [1];
        $this->expectException(DacapoErrorException::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails04a()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setPgConnectForceNew(true);
        $ds->setUseDacapoErrorHandler(false);
        $sql         = 'SELECT * FROM test.customers_en WHERE id=? AND lastname=?';
        $bind_params = [1];
        $this->expectException(Warning::class);
        $ds->select($sql, $bind_params);
    }
}
