Dacapo
======

Dacapo class (Simple PHP database wrapper)

Copyright Christos Pontikis http://www.pontikis.net

License MIT https://raw.github.com/pontikis/dacapo/master/MIT_LICENSE

Overview - Database
-------------------

* Supported RDMBS: MySQLi (or MariaDB), POSTGRESQL
* Simple and clear syntax
* Only prepared statements are used
* Supported Queries: single SELECT, UPDATE, INSERT, DELETE.
* Support of transactions
* Write SQL easily and securely. Use dacapo ``sql_placeholder`` (? is the default) in place of parameters values. Dacapo will create SQL prepared statements from standard ANSI SQL.

```php
$sql = 'SELECT procuct_name FROM products WHERE manufacturer = ? and type IN (?,?,?)';
```

### Remarks 
 
* For MYSQLi SELECT prepared statements, `mysqlnd` is required
* Persistent database connection NOT supported.
* BLOB columns NOT supported
* avoid boolean columns, use integer instead (1,0)
* Use ``$ds->execute()`` to execute one or usually multiple SQL statements (e.g. an SQL script). You cannot use prepared statements here.

### About Exceptions

You SHOULD create custom wrappers in your application to catch exceptions.

Dacapo Error Handler will throw `DacapoErrorException`.

If you choose to not use Dacapo Error Handler you will define type of Exception in your own Error Handler.

### About Postgresql sequences

When you execute an INSERT query in Postgres you have also to query a sequence if you want to get the last inserted value in Primary Key column. In this case use `setQueryInsertPgSequence()`. There are three options:

* `self::PG_SEQUENCE_NAME_AUTO` in this case sequence name will be automatically constructed as `tablename_id_seq`. This is the default setting (ideal for SERIAL columns)
* `null` (in the rare case when no Primary key is defined)
* the sequence real name 

REMEMBER that `query_insert_pg_sequence` will be reset to default `self::PG_SEQUENCE_NAME_AUTO` after each INSERT query.

Documentation
-------------

For HTML documentation, see ``docs/doxygen/html/`` folder (open ``index.html`` file in a browser).

Install 
--------

using Composer (recommended)

```bash
composer require pontikis/dacapo
```

or the old-school way:

```php
require_once 'path/to/Dacapo.php';
require_once 'path/to/DacapoErrorException.php';
```

Usage - examples
----------------

### Create instance

```php
use Pontikis\Database\Dacapo;
use Pontikis\Database\DacapoErrorException;

$db_settings = [
	'rdbms' => Dacapo::RDBMS_POSTGRES, // or Dacapo::RDBMS_MYSQLI for MySQL/MariaDB
	'db_server' => 'localhost',
	'db_user' => 'foo',
	'db_passwd' => 'bar',
	'db_name' => 'baz',
];

try {
	$ds = new Dacapo($db_settings);	
} catch (Exception $e) {
	// your code here
}
```

### Select

```php
$sql = 'SELECT id, firstname, lastname FROM customers WHERE lastname LIKE ?';
$bind_params = ['%' . $str . '%'];
try {
	$ds->select($sql, $bind_params);
	$customers = $ds->getData();
} catch (DacapoErrorException $e) {
	// your code here
}
```
#### Iterate data

```php
if($ds->getNumRows() > 0) {
	foreach($customers as $customer) {
		$id = $customer['id'];
		$lastname = $customer['lastname'];
		$firstname = $customer['firstname'];
	}	
}
```

### Select row

```php
$sql = 'SELECT firstname, lastname FROM customers WHERE id = ?';
$bind_params = [$id];
try {
	$ds->select($sql, $bind_params);
	if(1 === $ds->getNumRows()) {
		$customer = $ds->getRow();		
		$firstname = $customer['firstname'];
		$lastname = $customer['lastname'];
	}
} catch (DacapoErrorException $e) {
	// your code here
}
```

### Insert

```php
$sql = 'INSERT INTO customers (firstname, lastname) VALUES (?,?)';
$bind_params = [$firstname, $lastname];
try {
	$ds->insert($sql, $bind_params);
	$new_customer_id = $ds->getInsertId();
} catch (DacapoErrorException $e) {
	// your code here
}
```

### Update

```php
$sql = 'UPDATE customers SET category = ? WHERE balance > ?';
$bind_params = [$category, $balance];
try {
	$ds->update($sql, $bind_params);
	$affected_rows = $ds->getAffectedRows();
} catch (DacapoErrorException $e) {
	// your code here
}
```

### Delete

```php
$sql = 'DELETE FROM customers WHERE category = ?';
$bind_params = [$category];
try {
	$ds->delete($sql, $bind_params);
	$affected_rows = $ds->getAffectedRows();	
} catch (DacapoErrorException $e) {
	// your code here
}
```

### Transactions

```php
try {
	$ds->beginTrans();

	// delete from customers
	$sql = 'DELETE FROM customers WHERE id = ?';
	$bind_params = [$customers_id];
	$ds->delete($sql, $bind_params);

	// delete from demographics
	$sql = 'DELETE FROM demographics WHERE id = ?';
	$bind_params = [$customer_demographics_id];
	$ds->delete($sql, $bind_params);

	$ds->commitTrans();
} catch (DacapoErrorException $e) {
	$ds->rollbackTrans();
	// your code here
}
```

### Utility functions

#### lower

```php
// check for unique username (CASE IN-SENSITIVE)
$sql = "SELECT count('id') as total_rows FROM users WHERE {$ds->lower('username')} = ?";
$bind_params = [mb_strtolower($username)];
$ds->select($sql, $bind_params);
if($ds->getNumRows() > 0) {
	echo 'Username in use...';
}
```
#### limit

```php
$limitSQL = $ds->limit($rows_per_page, ($page_num - 1) * $rows_per_page);
```

PHPUnit
-------

Tests performed in Debian 9 Linux server with 
* php 7
* MariaDB Ver 15.1 Distrib 10.1.26-MariaDB (similar to MySQL 5.7)
* Postgres 9.6.7

Test databases are provided in `tests/dbdata` folder. Customize credentials in `tests/phpunit.xml`. First copy `phpunit.dest.xml` to `phpunit.xml`

### MySQL tests

## Test `connect` and `select`

```
./vendor/bin/phpunit --configuration tests/phpunit.xml tests/MySQLTest.php
```

mysqli timout make some tests slow. Run them once and then use:

```
./vendor/bin/phpunit --enforce-time-limit --configuration tests/phpunit.xml tests/MySQLTest.php
```

In this case PHP_Invoker is needed https://github.com/sebastianbergmann/php-invoker

## CUD tests Insert (C) Update (U) and Delete (D) operations and Transactions

```
./vendor/bin/phpunit --configuration tests/phpunit.xml tests/MySQLCUDTest.php
```

### Postgres tests

## Test `connect` and `select`

```
./vendor/bin/phpunit  --configuration tests/phpunit.xml tests/PostgresqlTest.php
```

## CUD tests Insert (C) Update (U) and Delete (D) operations and Transactions

```
./vendor/bin/phpunit  --configuration tests/phpunit.xml tests/PostgresqlCUDTest.php
```
### Run certain test eg testConnectFails1()

```
./vendor/bin/phpunit  --configuration tests/phpunit.xml tests/PostgresqlTest.php --filter '/testConnectFails1$/'
```

You cannot use `--filter` with *CUD* tests. Actually every *CUD* test depends on previous.

Contribution
------------

Your contribution is welcomed.

* Pull requests are accepted only in `dev` branch. 
* Remember to also submit the relevant PHPUnit tests. 
* Review is always required.