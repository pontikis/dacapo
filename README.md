Dacapo
======

Dacapo class (Simple PHP database and memcached wrapper)

Copyright Christos Pontikis http://www.pontikis.net

License MIT https://raw.github.com/pontikis/dacapo/master/MIT_LICENSE


Install 
--------

using Composer (recommended)

```bash
composer require pontikis/dacapo
```

or the old-school way:

```php
require_once 'path/to/Dacapo.php';
```

Overview - features
-------------------

* Supported RDMBS: MySQLi (or MariaDB), POSTGRESQL
* Simple and clear syntax
* Support of prepared statements
* Support of transactions
* Use Memcached https://memcached.org/ to cache results (optional)
* Write SQL easily and securely. Use dacapo ``sql_placeholder`` (? is the default) in place of column values. Dacapo will create SQL prepared statements. Otherwise, set dacapo ``direct_sql`` to true (false is the default)

```php
$sql = 'SELECT procuct_name FROM products WHERE manufacturer = ? and type IN (?,?,?)';
```
* Use ``$ds->execute()`` to execute one or usually multiple SQL statements (e.g. an SQL script). You cannot use prepared statements here.

### Remarks 
 
* For MYSQLi SELECT prepared statements, mysqlnd is required
* Persistent database connection NOT supported.
* BLOB columns NOT supported
* avoid boolean columns, use integer instead (1,0)

Documenation
------------

For HTML documentation, see ``docs/doxygen/html/`` folder (open ``index.html`` file in a browser).

Usage - examples
----------------

### Create instance

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

try {
	$ds = new dacapo($db_settings, $memcached_settings);	
} catch (Exception $e) {
	// your code here
}
```

### Select

```php
$sql = 'SELECT id, firstname, lastname FROM customers WHERE lastname LIKE ?';
$bind_params = array('%' . $str . '%');
try {
	$ds->select($sql, $bind_params);
	$customers = $ds->getData();
} catch (Exception $e) {
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
$bind_params = array($id);
$query_options = array("get_row" => true);
try {
	$ds->select($sql, $bind_params, $query_options);
	$customer = $ds->getData();	
} catch (Exception $e) {
	// your code here
}
```

#### Get row data

```php
if($ds->getNumRows() == 1) {
    $firstname = $customer['firstname'];
    $lastname = $customer['lastname'];
}
```

### Insert

```php
$sql = 'INSERT INTO customers (firstname, lastname) VALUES (?,?)';
$bind_params = array($firstname, $lastname);
try {
	$ds->insert($sql, $bind_params);
	$new_customer_id = $ds->getInsertId();
} catch (Exception $e) {
	// your code here
}
```

### Update

```php
$sql = 'UPDATE customers SET category = ? WHERE balance > ?';
$bind_params = array($category, $balance);
try {
	$ds->update($sql, $bind_params);
	$affected_rows = $ds->getAffectedRows();
} catch (Exception $e) {
	// your code here
}
```

### Delete

```php
$sql = 'DELETE FROM customers WHERE category = ?';
$bind_params = array($category);
try {
	$ds->delete($sql, $bind_params);
	$affected_rows = $ds->getAffectedRows();	
} catch (Exception $e) {
	// your code here
}
```

### Transactions

```php
$ds->beginTrans();

// delete from customers
$sql = 'DELETE FROM customers WHERE id = ?';
$bind_params = array($customers_id);
try {
	$ds->delete($sql, $bind_params);
} catch (Exception $e) {
	$ds->rollbackTrans();
	// your code here
}

// delete from demographics
$sql = 'DELETE FROM demographics WHERE id = ?';
$bind_params = array($customer_demographics_id);
try {
	$ds->delete($sql, $bind_params);
} catch (Exception $e) {
	$ds->rollbackTrans();
	// your code here
}

$ds->commitTrans();
```

### Memcached

```php
$mc_key_orders = 'orders_completed';
$orders = $ds->pull_from_memcached($mc_key_orders);
if(!$orders) {
	$sql = 'SELECT * FROM orders WHERE category = ?';
	$bind_params = array($category);
	$res = $ds->select($sql, $bind_params);
	if(!$res) {
		trigger_error($ds->getLastError(), E_USER_ERROR);
	}
	$orders = $ds->getData();

	$ds->push_to_memcached($mc_key_orders, $orders);
}

// after order insert or delete
$ds->delete_from_memcached($mc_key_orders);
```

### Utility functions

#### lower

```php
// check for unique username (CASE IN-SENSITIVE)
$sql = "SELECT count('id') as total_rows FROM users WHERE {$ds->lower('username')} = ?";
$bind_params = array(mb_strtolower($username));
$query_options = array('get_row' => true);
$res = $ds->select($sql, $bind_params, $query_options);
if(!$res) {
	trigger_error($ds->getLastError(), E_USER_ERROR);
}
if($ds->getNumRows() > 0) {
	echo 'Username in use...';
}
```
#### limit

```php
$limitSQL = $ds->limit($rows_per_page, ($page_num - 1) * $rows_per_page);
```

#### qstr

Escape and Quote string to be safe for SQL queries.

```php
$safeSQL = $ds->qstr($str);
```

However, use of preapared statements is strongly recommended in all cases. 

PHPUnit
-------

MySQL tests

```
./vendor/bin/phpunit --enforce-time-limit --configuration tests/phpunit.xml tests/MySQLTest.php
```

In this case PHP_Invoker is needed https://github.com/sebastianbergmann/php-invoker

Postgres tests

```
./vendor/bin/phpunit  --configuration tests/phpunit.xml tests/PostgresqlTest.php
```

Run certain test eg testConnectFails1()

```
./vendor/bin/phpunit  --configuration tests/phpunit.xml tests/PostgresqlTest.php --filter '/testConnectFails1$/'
```