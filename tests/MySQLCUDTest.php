<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use Pontikis\Database\Dacapo;

final class MySQLCUDTest extends TestCase
{
    protected static $db;

    protected static $mc;

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
        $ds  = new Dacapo(self::$db, self::$mc);
        $sql = 'DROP TABLE IF EXISTS `customers`;';
        $ds->execute($sql);
    }

    ////////////////////////////////////////////////////////////////////
    // Test instance                                                  //
    ////////////////////////////////////////////////////////////////////
    public function testSelect01()
    {
        $ds            = new Dacapo(self::$db, self::$mc);
        $sql           = 'SELECT * FROM customers';
        $bind_params   = [];
        $ds->select($sql, $bind_params);
        $this->assertSame(
            0,
            $ds->getNumRows()
        );
    }

    public function testInsert01()
    {
        $ds            = new Dacapo(self::$db, self::$mc);
        $sql           = 'INSERT INTO customers (lastname, firstname, gender, address) VALUES (?,?,?,?)';
        $bind_params   = [
            'Robertson',
            'Jerry',
            1,
            '01173 Doe Crossing Hill, Texas, 77346, United States',
        ];
        $ds->insert($sql, $bind_params);

        $ds->setFetchRow(true);
        $sql           = 'SELECT * FROM customers WHERE id=?';
        $bind_params   = [1];
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
}
