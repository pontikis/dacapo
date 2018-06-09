<?php

declare(strict_types=1);

//use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;
use Pontikis\Database\Dacapo;

//use Pontikis\Database\DacapoErrorException;

final class MemcachedTest extends TestCase
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
    }

    ////////////////////////////////////////////////////////////////////
    // Test instance                                                  //
    ////////////////////////////////////////////////////////////////////
    public function testInstance01()
    {
        $ds = new Dacapo(self::$db, self::$mc);

        $this->assertTrue(true);
    }
}
