<?php

declare(strict_types=1);

use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;
use Pontikis\Database\Dacapo;
use Pontikis\Database\DacapoErrorException;

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

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class                            //
    ////////////////////////////////////////////////////////////////////
    public static function setUpBeforeClass()
    {
        self::$db_with_server_name = [
            'rdbms'     => Dacapo::RDBMS_MYSQLI,
            'db_server' => $GLOBALS['MYSQL_SERVER_NAME'],
            'db_user'   => $GLOBALS['MYSQL_USER'],
            'db_passwd' => $GLOBALS['MYSQL_PASSWD'],
            'db_name'   => $GLOBALS['MYSQL_DBNAME'],
        ];

        self::$db_with_server_ip              = self::$db_with_server_name;
        self::$db_with_server_ip['db_server'] = $GLOBALS['MYSQL_SERVER_IP'];

        self::$db_wrong_rdbms          = self::$db_with_server_name;
        self::$db_wrong_rdbms['rdbms'] = $GLOBALS['RDBMS_WRONG'];

        self::$db_wrong_server_name              = self::$db_with_server_name;
        self::$db_wrong_server_name['db_server'] = $GLOBALS['MYSQL_SERVER_NAME_WRONG'];

        self::$db_wrong_server_ip              = self::$db_with_server_name;
        self::$db_wrong_server_ip['db_server'] = $GLOBALS['MYSQL_SERVER_IP_WRONG'];

        self::$db_wrong_user_with_server_name            = self::$db_with_server_name;
        self::$db_wrong_user_with_server_name['db_user'] = $GLOBALS['MYSQL_USER_WRONG'];

        self::$db_wrong_user_with_server_ip            = self::$db_with_server_ip;
        self::$db_wrong_user_with_server_ip['db_user'] = $GLOBALS['MYSQL_USER_WRONG'];

        self::$db_wrong_passwd_with_server_name              = self::$db_with_server_name;
        self::$db_wrong_passwd_with_server_name['db_passwd'] = $GLOBALS['MYSQL_PASSWD_WRONG'];

        self::$db_wrong_passwd_with_server_ip              = self::$db_with_server_ip;
        self::$db_wrong_passwd_with_server_ip['db_passwd'] = $GLOBALS['MYSQL_PASSWD_WRONG'];

        self::$db_wrong_dbname_with_server_name            = self::$db_with_server_name;
        self::$db_wrong_dbname_with_server_name['db_name'] = $GLOBALS['MYSQL_DBNAME_WRONG'];

        self::$db_wrong_dbname_with_server_ip            = self::$db_with_server_ip;
        self::$db_wrong_dbname_with_server_ip['db_name'] = $GLOBALS['MYSQL_DBNAME_WRONG'];
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

        $this->assertInstanceOf(
            mysqli::class,
            $ds->dbConnect()
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setDbPort((int) $GLOBALS['MYSQL_PORT']);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertInstanceOf(
            mysqli::class,
            $ds->dbConnect()
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertInstanceOf(
            mysqli::class,
            $ds->dbConnect()
        );
    }

    public function testConnect02()
    {
        $ds = new Dacapo(self::$db_with_server_ip);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertInstanceOf(
            mysqli::class,
            $ds->dbConnect()
        );

        $ds = new Dacapo(self::$db_with_server_ip);
        $ds->setDbPort((int) $GLOBALS['MYSQL_PORT']);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertInstanceOf(
            mysqli::class,
            $ds->dbConnect()
        );

        $ds = new Dacapo(self::$db_with_server_ip);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);

        $this->assertInstanceOf(
            Dacapo::class,
            $ds
        );

        $this->assertInstanceOf(
            mysqli::class,
            $ds->dbConnect()
        );
    }

    /**
     * This test will take more than a minute to be executed
     * (mysqli connection timeout).
     * To avoid this use --enforce-time-limit.
     * (you cannot set mysqli connect timeout unless you use mysqli_real_connect()).
     *
     * @small
     */
    public function testConnectFails01()
    {
        $ds = new Dacapo(self::$db_wrong_server_name);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    /**
     * @small
     */
    public function testConnectFails01a()
    {
        $ds = new Dacapo(self::$db_wrong_server_name);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    /**
     * This test will take more than a minute to be executed
     * (mysqli connection timeout).
     * To avoid this use --enforce-time-limit.
     * (you cannot set mysqli connect timeout unless you use mysqli_real_connect()).
     *
     * @small
     */
    public function testConnectFails02()
    {
        $ds = new Dacapo(self::$db_wrong_server_ip);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    /**
     * @small
     */
    public function testConnectFails02a()
    {
        $ds = new Dacapo(self::$db_wrong_server_ip);
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
        if ('localhost' === $GLOBALS['MYSQL_SERVER_NAME']) {
            $this->markTestSkipped(
                'Using localhost, port is ignored.'
            );
        }

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setDbPort((int) $GLOBALS['MYSQL_PORT_WRONG']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails09a()
    {
        if ('localhost' === $GLOBALS['MYSQL_SERVER_NAME']) {
            $this->markTestSkipped(
                'Using localhost, port is ignored.'
            );
        }

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setDbPort((int) $GLOBALS['MYSQL_PORT_WRONG']);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    public function testConnectFails10()
    {
        $ds = new Dacapo(self::$db_with_server_ip);
        $ds->setDbPort((int) $GLOBALS['MYSQL_PORT_WRONG']);
        $this->expectException(DacapoErrorException::class);
        $ds->dbConnect();
    }

    public function testConnectFails10a()
    {
        $ds = new Dacapo(self::$db_with_server_ip);
        $ds->setDbPort((int) $GLOBALS['MYSQL_PORT_WRONG']);
        $ds->setUseDacapoErrorHandler(false);
        $this->expectException(Warning::class);
        $ds->dbConnect();
    }

    ////////////////////////////////////////////////////////////////////
    // Test select()                                                  //
    ////////////////////////////////////////////////////////////////////
    public function testSelect01()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $sql           = 'SELECT * FROM customers_en';
        $bind_params   = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            100,
            $ds->getNumRows()
        );
    }

    public function testSelect01el()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $sql           = 'SELECT * FROM customers_el';
        $bind_params   = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            105,
            $ds->getNumRows()
        );
    }

    public function testSelect02()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $sql           = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $sql           = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET_WRONG']);
        $sql           = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );
    }

    public function testSelect02el()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET_WRONG']);
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertNotEquals(
            'Γεωργίου',
            $row['lastname']
        );
    }

    public function testSelect03()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $ds->setFetchTypeAssoc();
        $sql           = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $ds->setFetchTypeNum();
        $sql           = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Robertson',
            $row[0]
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $ds->setFetchTypeBoth();
        $sql           = 'SELECT lastname FROM customers_en WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
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
        $ds            = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $ds->setFetchTypeAssoc();
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $ds->setFetchTypeNum();
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            'Γεωργίου',
            $row[0]
        );

        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setFetchRow(true);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $ds->setFetchTypeBoth();
        $sql           = 'SELECT lastname FROM customers_el WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
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
        $ds            = new Dacapo(self::$db_with_server_name);
        $sql           = 'SELECT * FROM customers_xx';
        $bind_params   = [];
        $this->expectException(DacapoErrorException::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails01a()
    {
        $ds = new Dacapo(self::$db_with_server_name);
        $ds->setUseDacapoErrorHandler(false);
        $sql           = 'SELECT * FROM customers_xx';
        $bind_params   = [];
        $this->expectException(Warning::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails02()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $sql           = 'SELECT * FROM customers_en WHERE wrong_column=?';
        $bind_params   = [1];
        $this->expectException(DacapoErrorException::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails02a()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $ds->setUseDacapoErrorHandler(false);
        $sql           = 'SELECT * FROM customers_en WHERE wrong_column=?';
        $bind_params   = [1];
        $this->expectException(Warning::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails03()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $sql           = 'SELECT * FROM customers_en WHERE id=?';
        $bind_params   = [1, 2];
        $this->expectException(DacapoErrorException::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails03a()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $ds->setUseDacapoErrorHandler(false);
        $sql           = 'SELECT * FROM customers_en WHERE id=?';
        $bind_params   = [1, 2];
        $this->expectException(Warning::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails04()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $sql           = 'SELECT * FROM customers_en WHERE id=? AND lastname=?';
        $bind_params   = [1];
        $this->expectException(DacapoErrorException::class);
        $ds->select($sql, $bind_params);
    }

    public function testSelectFails04a()
    {
        $ds            = new Dacapo(self::$db_with_server_name);
        $ds->setUseDacapoErrorHandler(false);
        $sql           = 'SELECT * FROM customers_en WHERE id=? AND lastname=?';
        $bind_params   = [1];
        $this->expectException(Warning::class);
        $ds->select($sql, $bind_params);
    }
}
