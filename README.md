dacapo
======

Da Capo class (Simple PHP database and memcached wrapper)

Copyright Christos Pontikis http://www.pontikis.net

License MIT https://raw.github.com/pontikis/dacapo/master/MIT_LICENSE

 * Supported RDMBS: MySQLi (or MariaDB), POSTGRESQL
 * For MYSQLi SELECT prepared statements, mysqlnd is required
 * Persistent database connection NOT supported.
 * BLOB columns NOT supported
 * avoid boolean columns, use integer instead (1,0)

Documenation
------------

See ``docs`` folder

Usage
-----

```php
require_once '/path/to/dacapo.class.php';

$db_settings = array(
	'rdbms' => 'POSTGRES', // or 'MYSQLi' for MySQL/MariaDB
	'db_server' => 'localhost',
	'db_user' => 'foo',
	'db_passwd' => 'foo',
	'db_name' => 'foo',
	'db_schema' => 'public', // POSTGRES only
	'db_port' => '5432', // or '3306' for MySQL/MariaDB
	'charset' => 'utf8',
	'use_pst' => true, // use prepared statements
	'pst_placeholder' => 'numbered' // or 'question_mark' for MySQL/MariaDB
);

$memcached_settings = array(
	'mc_pool' => array(
		array(
			'mc_server' => '127.0.0.1',
			'mc_port' => '11211',
			'mc_weight' => 0
		)
	)
);

$ds = new dacapo($db_settings, $memcached_settings);
```