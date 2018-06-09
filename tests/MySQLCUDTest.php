<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pontikis\Database\Dacapo;

final class MySQLCUDTest extends TestCase
{
    protected static $db;

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class                            //
    ////////////////////////////////////////////////////////////////////
    public static function setUpBeforeClass()
    {
        self::$db = [
            'rdbms'     => Dacapo::RDBMS_MYSQLI,
            'db_server' => $GLOBALS['MYSQL_SERVER_NAME'],
            'db_user'   => $GLOBALS['MYSQL_USER'],
            'db_passwd' => $GLOBALS['MYSQL_PASSWD'],
            'db_name'   => $GLOBALS['MYSQL_DBNAME'],
        ];

        $ds = new Dacapo(self::$db);

        $sql = 'DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `lastname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `firstname` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `fathername` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `gender` tinyint(4) DEFAULT NULL,
  `address` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;';

        $ds->execute($sql);
    }

    ////////////////////////////////////////////////////////////////////
    // Basic setup - it runs once in Class (after tests)              //
    ////////////////////////////////////////////////////////////////////
    public static function tearDownAfterClass()
    {
        if (1 === (int) $GLOBALS['MYSQL_DROP_TABLES_CREATED_FOR_UPDATE']) {
            $ds  = new Dacapo(self::$db);
            $sql = 'DROP TABLE IF EXISTS `customers`;';
            $ds->execute($sql);
        }
    }

    ////////////////////////////////////////////////////////////////////
    // Test select                                                    //
    ////////////////////////////////////////////////////////////////////
    public function testSelect01()
    {
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $sql         = 'SELECT * FROM customers';
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
     * @depends testSelect01
     */
    public function testInsert01()
    {
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $sql         = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params = [
            'Robertson',
            'Jerry',
            1,
            '01173 Doe Crossing Hill, Texas, 77346, United States',
        ];
        $ds->insert($sql, $bind_params);
        $this->assertSame(
            1,
            $ds->getInsertId()
        );
        $this->assertSame(
            1,
            $ds->getAffectedRows()
        );

        $ds->setFetchRow(true);
        $sql         = 'SELECT * FROM customers WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            1,
            $row['id']
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
            $row['gender']
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
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $sql         = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params = [
            'Γεωργίου',
            'Γεώργιος',
            1,
            'Γεωργίου Σεφέρη 35, Νεάπολη Συκεές, 567 28, Θεσσαλονίκη, Ελλάδα',
        ];
        $ds->insert($sql, $bind_params);
        $this->assertSame(
            2,
            $ds->getInsertId()
        );
        $this->assertSame(
            1,
            $ds->getAffectedRows()
        );

        $ds->setFetchRow(true);
        $sql         = 'SELECT * FROM customers WHERE id=?';
        $bind_params = [2];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            2,
            $row['id']
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
            $row['gender']
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
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $sql         = 'UPDATE customers SET lastname = ?, firstname = ?, gender = ?, address = ? WHERE id = ?';
        $bind_params = [
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
        $sql         = 'SELECT * FROM customers WHERE id=?';
        $bind_params = [1];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            1,
            $row['id']
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
            $row['gender']
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
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $sql         = 'UPDATE customers SET lastname = ?, firstname = ?, gender = ?, address = ? WHERE id = ?';
        $bind_params = [
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
        $sql         = 'SELECT * FROM customers WHERE id=?';
        $bind_params = [2];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            2,
            $row['id']
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
            $row['gender']
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
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);
        $sql         = 'DELETE FROM customers WHERE id IN (?,?)';
        $bind_params = [
            1,
            2,
        ];
        $ds->delete($sql, $bind_params);
        $this->assertSame(
            2,
            $ds->getAffectedRows()
        );

        $sql         = 'SELECT * FROM customers';
        $bind_params = [];
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
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);

        $ds->beginTrans();

        $sql         = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params = [
            'Fowler',
            'Jeremy',
            1,
            '23 Dottie Trail, Virginia, 20189, United States',
        ];
        $ds->insert($sql, $bind_params);

        $ds->rollbackTrans();

        $sql         = 'SELECT * FROM customers WHERE lastname=? AND firstname=?';
        $bind_params = [
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
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);

        $ds->beginTrans();

        $sql         = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params = [
            'Fowler',
            'Jeremy',
            1,
            '23 Dottie Trail, Virginia, 20189, United States',
        ];
        $ds->insert($sql, $bind_params);

        $ds->commitTrans();

        $ds->setFetchRow(true);
        $sql         = 'SELECT * FROM customers WHERE lastname=? AND firstname=?';
        $bind_params = [
            'Fowler',
            'Jeremy',
        ];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            4,
            $row['id']
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
            $row['gender']
        );
        $this->assertSame(
            '23 Dottie Trail, Virginia, 20189, United States',
            $row['address']
        );
    }

    /**
     * @depends testTransactions02
     */
    public function testTransactions03()
    {
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);

        try {
            $ds->beginTrans();

            $sql         = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
            $bind_params = [
                'Johnston',
                'Patrick',
                1,
                '03 Scott Terrace, Nevada, 89120, United States',
            ];
            $ds->insert($sql, $bind_params);

            $sql         = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
            $bind_params = [
                'Gardner',
                'Shawn',
                1,
                '988 Wayridge Park, Arizona, 85255, United States',
            ];
            $ds->insert($sql, $bind_params);

            throw new Exception('Transcaction aborted');
            $ds->commitTrans();
        } catch (Exception $e) {
            $ds->rollbackTrans();
            $this->expectOutputString('Transcaction aborted');
            echo $e->getMessage();
        }
    }

    /**
     * @depends testTransactions02
     */
    public function testTransactions04()
    {
        $ds = new Dacapo(self::$db);
        $ds->setCharset($GLOBALS['MYSQL_CHARSET']);

        try {
            $ds->beginTrans();

            $sql         = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
            $bind_params = [
                'Johnston',
                'Patrick',
                1,
                '03 Scott Terrace, Nevada, 89120, United States',
            ];
            $ds->insert($sql, $bind_params);

            $sql         = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
            $bind_params = [
                'Gardner',
                'Shawn',
                1,
                '988 Wayridge Park, Arizona, 85255, United States',
            ];
            $ds->insert($sql, $bind_params);

            $ds->commitTrans();
        } catch (Exception $e) {
            $ds->rollbackTrans();
        }

        $ds->setFetchRow(true);
        $sql         = 'SELECT * FROM customers WHERE lastname=? AND firstname=?';
        $bind_params = [
            'Johnston',
            'Patrick',
        ];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            7,
            $row['id']
        );
        $this->assertSame(
            '03 Scott Terrace, Nevada, 89120, United States',
            $row['address']
        );

        $sql         = 'SELECT * FROM customers WHERE lastname=? AND firstname=?';
        $bind_params = [
            'Gardner',
            'Shawn',
        ];
        $ds->select($sql, $bind_params);
        $row = $ds->getData();
        $this->assertSame(
            8,
            $row['id']
        );
        $this->assertSame(
            '988 Wayridge Park, Arizona, 85255, United States',
            $row['address']
        );
    }
}
