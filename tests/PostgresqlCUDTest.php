<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pontikis\Database\Dacapo;

final class PostgresqlCUDTest extends TestCase
{
    protected static $db;

    protected static $mc;

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class (before tests)             //
    ////////////////////////////////////////////////////////////////////
    public static function setUpBeforeClass()
    {
        self::$db = [
            'rdbms'     => Dacapo::RDBMS_POSTGRES,
            'db_server' => $GLOBALS['POSTGRES_SERVER_NAME'],
            'db_user'   => $GLOBALS['POSTGRES_USER'],
            'db_passwd' => $GLOBALS['POSTGRES_PASSWD'],
            'db_name'   => $GLOBALS['POSTGRES_DBNAME'],
        ];

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

        $ds = new Dacapo(self::$db, self::$mc);
        $ds->setPgConnectForceNew(true);
        $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);

        $sql = "DROP TABLE IF EXISTS customers CASCADE;
CREATE TABLE customers (
    id integer NOT NULL,
    lastname character varying(100) NOT NULL,
    firstname character varying(100) NOT NULL,
    fathername character varying(100),
    gender integer,
    address character varying(200)
);
ALTER TABLE customers OWNER TO testdb;
CREATE SEQUENCE customers_id_seq
    START WITH 1
    INCREMENT BY 1
    NO MINVALUE
    NO MAXVALUE
    CACHE 1;
ALTER TABLE customers_id_seq OWNER TO testdb;
ALTER SEQUENCE customers_id_seq OWNED BY customers.id;
ALTER TABLE ONLY customers ALTER COLUMN id SET DEFAULT nextval('customers_id_seq'::regclass);
ALTER TABLE ONLY customers
    ADD CONSTRAINT customers_pkey PRIMARY KEY (id);";

        $ds->execute($sql);
    }

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class (after tests)              //
    ////////////////////////////////////////////////////////////////////
    public static function tearDownAfterClass()
    {
        if (1 === (int) $GLOBALS['POSTGRES_DROP_TABLES_CREATED_FOR_UPDATE']) {
            $ds = new Dacapo(self::$db, self::$mc);
            $ds->setPgConnectForceNew(true);
            $ds->setDbSchema($GLOBALS['POSTGRES_DBSCHEMA']);
            $sql = 'DROP TABLE IF EXISTS customers CASCADE;';
            $ds->execute($sql);
        }
    }

    ////////////////////////////////////////////////////////////////////
    // Test select                                                    //
    ////////////////////////////////////////////////////////////////////
    public function testSelect01()
    {
        $ds = new Dacapo(self::$db, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setPgConnectForceNew(true);
        $sql         = 'SELECT * FROM test.customers';
        $bind_params = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            0,
            $ds->getNumRows()
        );
    }

    ////////////////////////////////////////////////////////////////////
    // Test insert                                                    //
    ////////////////////////////////////////////////////////////////////

    /**
     * Note the difference (from mysqli::fetch_array) in pg_query_params: it return string values
     * even in numeric columns.
     *
     * @depends testSelect01
     */
    public function testInsert01()
    {
        $ds = new Dacapo(self::$db, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setPgConnectForceNew(true);
        $sql         = 'INSERT INTO test.customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params = [
            'Robertson',
            'Jerry',
            1,
            '01173 Doe Crossing Hill, Texas, 77346, United States',
        ];
        $ds->insert($sql, $bind_params);
        $this->assertSame(
            1,
            (int) $ds->getInsertId()
        );
        $this->assertSame(
            1,
            $ds->getAffectedRows()
        );

        $ds->setFetchRow(true);
        $sql         = 'SELECT * FROM test.customers WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            1,
            (int) $row['id']
        );
        $this->assertSame(
            'Robertson',
            $row['lastname']
        );
        $this->assertSame(
            'Jerry',
            $row['firstname']
        );
        $this->assertSame(
            1,
            (int) $row['gender']
        );
        $this->assertSame(
            '01173 Doe Crossing Hill, Texas, 77346, United States',
            $row['address']
        );
    }

    /**
     * @depends testInsert01
     */
    public function testInsert01el()
    {
        $ds = new Dacapo(self::$db, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setPgConnectForceNew(true);
        $sql         = 'INSERT INTO test.customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params = [
            'Γεωργίου',
            'Γεώργιος',
            1,
            'Γεωργίου Σεφέρη 35, Νεάπολη Συκεές, 567 28, Θεσσαλονίκη, Ελλάδα',
        ];
        $ds->insert($sql, $bind_params);
        $this->assertSame(
            2,
            (int) $ds->getInsertId()
        );
        $this->assertSame(
            1,
            $ds->getAffectedRows()
        );

        $ds->setFetchRow(true);
        $sql         = 'SELECT * FROM test.customers WHERE id=?';
        $bind_params = [2];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            2,
            (int) $row['id']
        );
        $this->assertSame(
            'Γεωργίου',
            $row['lastname']
        );
        $this->assertSame(
            'Γεώργιος',
            $row['firstname']
        );
        $this->assertSame(
            1,
            (int) $row['gender']
        );
        $this->assertSame(
            'Γεωργίου Σεφέρη 35, Νεάπολη Συκεές, 567 28, Θεσσαλονίκη, Ελλάδα',
            $row['address']
        );
    }

    ////////////////////////////////////////////////////////////////////
    // Test update                                                    //
    ////////////////////////////////////////////////////////////////////

    /**
     * @depends testInsert01
     */
    public function testUpdate01()
    {
        $ds            = new Dacapo(self::$db, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setPgConnectForceNew(true);
        $sql           = 'UPDATE test.customers SET lastname = ?, firstname = ?, gender = ?, address = ? WHERE id = ?';
        $bind_params   = [
            'Wallace',
            'Craig',
            1,
            '01173 Doe Crossing Hill, Texas, 77346, United States',
            1,
        ];
        $ds->update($sql, $bind_params);
        $this->assertSame(
            1,
            $ds->getAffectedRows()
        );

        $ds->setFetchRow(true);
        $sql           = 'SELECT * FROM test.customers WHERE id=?';
        $bind_params   = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            1,
            (int) $row['id']
        );
        $this->assertSame(
            'Wallace',
            $row['lastname']
        );
        $this->assertSame(
            'Craig',
            $row['firstname']
        );
        $this->assertSame(
            1,
            (int) $row['gender']
        );
        $this->assertSame(
            '01173 Doe Crossing Hill, Texas, 77346, United States',
            $row['address']
        );
    }

    /**
     * @depends testInsert01el
     */
    public function testUpdate01el()
    {
        $ds            = new Dacapo(self::$db, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setPgConnectForceNew(true);
        $sql           = 'UPDATE test.customers SET lastname = ?, firstname = ?, gender = ?, address = ? WHERE id = ?';
        $bind_params   = [
            'Γεωργόπουλος',
            'Βασίλειος',
            1,
            null,
            2,
        ];
        $ds->update($sql, $bind_params);
        $this->assertSame(
            1,
            $ds->getAffectedRows()
        );

        $ds->setFetchRow(true);
        $sql           = 'SELECT * FROM test.customers WHERE id=?';
        $bind_params   = [2];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            2,
            (int) $row['id']
        );
        $this->assertSame(
            'Γεωργόπουλος',
            $row['lastname']
        );
        $this->assertSame(
            'Βασίλειος',
            $row['firstname']
        );
        $this->assertSame(
            1,
            (int) $row['gender']
        );
        $this->assertNull(
            $row['address']
        );
    }

    ////////////////////////////////////////////////////////////////////
    // Test delete                                                    //
    ////////////////////////////////////////////////////////////////////

    /**
     * @depends testInsert01
     * @depends testInsert01el
     */
    public function testDelete01()
    {
        $ds            = new Dacapo(self::$db, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setPgConnectForceNew(true);
        $sql           = 'DELETE FROM test.customers WHERE id IN (?,?)';
        $bind_params   = [
            1,
            2,
        ];
        $ds->delete($sql, $bind_params);
        $this->assertSame(
            2,
            $ds->getAffectedRows()
        );

        $sql           = 'SELECT * FROM test.customers';
        $bind_params   = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            0,
            $ds->getNumRows()
        );
    }

    ////////////////////////////////////////////////////////////////////
    // Test transactions                                              //
    ////////////////////////////////////////////////////////////////////

    /**
     * @depends testInsert01
     * @depends testInsert01el
     */
    public function testTransactions01()
    {
        $ds            = new Dacapo(self::$db, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setPgConnectForceNew(true);

        $ds->beginTrans();

        $sql           = 'INSERT INTO test.customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params   = [
            'Fowler',
            'Jeremy',
            1,
            '23 Dottie Trail, Virginia, 20189, United States',
        ];
        $ds->insert($sql, $bind_params);

        $ds->rollbackTrans();

        $sql           = 'SELECT * FROM test.customers WHERE lastname=? AND firstname=?';
        $bind_params   = [
            'Fowler',
            'Jeremy',
        ];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            0,
            $ds->getNumRows()
        );
    }

    /**
     * @depends testInsert01
     * @depends testInsert01el
     * @depends testTransactions01
     */
    public function testTransactions02()
    {
        $ds            = new Dacapo(self::$db, self::$mc);
        $ds->setCharset($GLOBALS['POSTGRES_CHARSET']);
        $ds->setPgConnectForceNew(true);

        $ds->beginTrans();

        $sql           = 'INSERT INTO test.customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params   = [
            'Fowler',
            'Jeremy',
            1,
            '23 Dottie Trail, Virginia, 20189, United States',
        ];
        $ds->insert($sql, $bind_params);

        $ds->commitTrans();

        $ds->setFetchRow(true);
        $sql           = 'SELECT * FROM test.customers WHERE lastname=? AND firstname=?';
        $bind_params   = [
            'Fowler',
            'Jeremy',
        ];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            4,
            (int) $row['id']
        );
        $this->assertSame(
            'Fowler',
            $row['lastname']
        );
        $this->assertSame(
            'Jeremy',
            $row['firstname']
        );
        $this->assertSame(
            1,
            (int) $row['gender']
        );
        $this->assertSame(
            '23 Dottie Trail, Virginia, 20189, United States',
            $row['address']
        );
    }
}
