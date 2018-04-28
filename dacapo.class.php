<?php

/**
 * Da Capo class (Simple PHP database and memcached wrapper).
 *
 * Supported RDMBS: MySQLi, POSTGRESQL
 * For MYSQLi SELECT prepared statements, mysqlnd is required
 * Persistent database connection NOT supported.
 * BLOB columns NOT supported
 * avoid boolean columns, use integer instead (1,0)
 *
 * @copyright  Christos Pontikis
 * @license    http://opensource.org/licenses/MIT MIT
 *
 * @version    0.9.3 (28 Apr 2018)
 */
class dacapo
{
    private $rdbms;
    private $db_server;
    private $db_user;
    private $db_passwd;
    private $db_name;

    private $db_schema;
    private $db_port;
    private $charset;

    private $pg_connect_force_new;

    /** @var bool Use prepared statements or not */
    private $use_pst;
    private $pst_placeholder;

    /** @var bool Use full SQL statements or not */
    private $direct_sql;
    /** @var string variables placeholder in SQL statements */
    private $sql_placeholder;

    private $fetch_type;

    /** @var int|mixed 0 suppress errors (else use PHP error level predefined constants) */
    private $error_level;

    private $messages;

    // ***
    private $conn;
    private $sql;

    /** @var array data returned */
    private $data;
    private $num_rows;
    private $insert_id;
    private $affected_rows;

    private $a_fetch_type_mysql;
    private $a_fetch_type_postgres;

    private $last_error;
    private $last_errno;
    private $a_pst_placeholder;
    private $a_types;

    private $mc_settings;
    private $mc;

    /**
     * Constructor.
     *
     * @param array $a_db database settings
     * @param array $a_mc memcached settings
     */
    public function __construct($a_db, $a_mc)
    {
        $this->rdbms     = $a_db['rdbms'];
        $this->db_server = $a_db['db_server'];
        $this->db_user   = $a_db['db_user'];
        $this->db_passwd = $a_db['db_passwd'];
        $this->db_name   = $a_db['db_name'];

        $this->db_schema = array_key_exists('db_schema', $a_db) ? $a_db['db_schema'] : null;
        $this->db_port   = array_key_exists('db_port', $a_db) ? $a_db['db_port'] : null;
        $this->charset   = array_key_exists('charset', $a_db) ? $a_db['charset'] : null;

        $this->pg_connect_force_new = array_key_exists('pg_connect_force_new', $a_db) ? $a_db['pg_connect_force_new'] : false;

        $this->use_pst         = array_key_exists('use_pst', $a_db) ? $a_db['use_pst'] : false;
        $this->pst_placeholder = array_key_exists('pst_placeholder', $a_db) ? $a_db['pst_placeholder'] : 'auto';

        $this->direct_sql      = array_key_exists('direct_sql', $a_db) ? $a_db['direct_sql'] : false;
        $this->sql_placeholder = array_key_exists('sql_placeholder', $a_db) ? $a_db['sql_placeholder'] : '?';

        $this->fetch_type = array_key_exists('fetch_type', $a_db) ? $a_db['fetch_type'] : 'ASSOC';

        // 0 suppress errors (else use PHP error level predefined constants)
        $this->error_level = array_key_exists('error_level', $a_db) ? $a_db['error_level'] : 0;

        $this->messages = array_key_exists('messages', $a_db) ? $a_db['messages'] : [
            'db_not_supported'            => 'Dacapo ERROR: Database not supported',
            'invalid_placeholder'         => 'Dacapo ERROR: Invalid placeholder for prepared statements',
            'invalid_number_of_variables' => 'Dacapo ERROR: Number of variables (%u) does not match number of parameters in statement (%u)',
            'db_connect_error'            => 'Dacapo ERROR: Database connection error',
            'wrong_sql'                   => 'Dacapo ERROR: Query failed',
            'query_execution_error'       => 'Dacapo ERROR: Error executing query',
        ];

        // initialize ----------------------------------------------------------
        $this->conn          = null;
        $this->sql           = null;
        $this->data          = null;
        $this->num_rows      = null;
        $this->insert_id     = null;
        $this->affected_rows = null;
        $this->last_error    = null;
        $this->last_errno    = null;

        if (extension_loaded('mysqli')) {
            $this->a_fetch_type_mysql = [
                'ASSOC' => MYSQLI_ASSOC,
                'NUM'   => MYSQLI_NUM,
                'BOTH'  => MYSQLI_BOTH,
            ];
        }

        if (extension_loaded('pgsql')) {
            $this->a_fetch_type_postgres = [
                'ASSOC' => PGSQL_ASSOC,
                'NUM'   => PGSQL_NUM,
                'BOTH'  => PGSQL_BOTH,
            ];
        }

        $this->a_pst_placeholder = [
            'MYSQLi'   => 'question_mark',
            'POSTGRES' => 'numbered',
        ];

        // Bind parameters. Types: s = string, i = integer, d = double,  b = blob
        $this->a_types = [
            'string'  => 's',
            'integer' => 'i',
            'double'  => 'd',
            'boolean' => 'i', // avoid boolean in params, use integer instead (1,0)
            'NULL'    => 's', // do not need to cast null to a particular data type
        ];

        $this->mc_settings = $a_mc;
        $this->mc          = null;
    }

    // PUBLIC FUNCTIONS --------------------------------------------------------

    // getters -----------------------------------------------------------------

    /**
     * Get data returned from a select query.
     *
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get returned rows count.
     */
    public function getNumRows()
    {
        return $this->num_rows;
    }

    /**
     * Get inserted record id.
     *
     * @return int|null
     */
    public function getInsertId()
    {
        return $this->insert_id;
    }

    /**
     * Get affected rows count.
     *
     * @return int|null
     */
    public function getAffectedRows()
    {
        return $this->affected_rows;
    }

    /**
     * Get the symbol used for SQL placeholder.
     *
     * @return mixed|string
     */
    public function getSqlPlaceholder()
    {
        return $this->sql_placeholder;
    }

    /**
     * Get last error occured.
     *
     * @return string|null
     */
    public function getLastError()
    {
        return $this->last_error;
    }

    /**
     * Get last error number occured.
     *
     * @return int|null
     */
    public function getLastErrorNumber()
    {
        return $this->last_errno;
    }

    // setters -----------------------------------------------------------------

    /**
     * Set database schema.
     *
     * @param string $schema
     */
    public function setDbSchema($schema)
    {
        $this->db_schema = $schema;
    }

    /**
     * Set option.
     *
     * @param $opt
     * @param $val
     */
    public function set_option($opt, $val)
    {
        $a_valid_options = [
            'use_pst',
            'pst_placeholder',
            'direct_sql',
            'sql_placeholder',
            'fetch_type',
            'error_level',
            'messages',
        ];

        if (in_array($opt, $a_valid_options)) {
            $this->$opt = $val;
        }
    }

    /**
     * Set db connection.
     *
     * It might be useful only to MySQLi.
     *
     * Concerning Postgres (http://php.net/pg_connect): If a second call is made to pg_connect()
     * with the same connection_string as an existing connection,
     * the existing connection will be returned unless you pass PGSQL_CONNECT_FORCE_NEW as connect_type.
     *
     * @param $conn
     */
    public function set_conn($conn)
    {
        $this->conn = $conn;
    }

    // MAIN FUNCTIONS ----------------------------------------------------------

    /**
     * Establish database connection.
     *
     * @return bool|mysqli|object|resource|null
     */
    public function db_connect()
    {
        $this->last_error = null;
        $this->last_errno = null;

        // RDBMS not supported
        if (!in_array($this->rdbms, ['MYSQLi', 'POSTGRES'])) {
            $this->last_error = $this->messages['db_not_supported'] . ': ' . $this->rdbms;
            $this->_trigger_error();

            return false;
        }

        // Invalid placeholder for prepared statements
        if ($this->use_pst && !in_array($this->pst_placeholder, ['question_mark', 'numbered', 'auto'])) {
            $this->last_error = $this->messages['invalid_placeholder'];
            $this->_trigger_error();

            return false;
        }

        if (null === $this->conn) {
            if ('MYSQLi' == $this->rdbms) {
                try {
                    if ($this->db_port) {
                        $conn = new mysqli($this->db_server, $this->db_user, $this->db_passwd, $this->db_name, $this->db_port);
                    } else {
                        $conn = new mysqli($this->db_server, $this->db_user, $this->db_passwd, $this->db_name);
                    }
                    if ($this->charset) {
                        $conn->set_charset($this->charset);
                    }
                    $this->conn = $conn;
                } catch (Exception $e) {
                    $this->last_error = $this->messages['db_connect_error'] . ': ' . $e->getMessage();
                    $this->_trigger_error();
                }
            }

            if ('POSTGRES' == $this->rdbms) {
                $dsn = 'host=' . $this->db_server . ' port=' . $this->db_port . ' dbname=' . $this->db_name .
                ' user=' . $this->db_user . ' password=' . $this->db_passwd;

                try {
                    if ($this->pg_connect_force_new) {
                        $conn = pg_connect($dsn, PGSQL_CONNECT_FORCE_NEW);
                    } else {
                        $conn = pg_connect($dsn);
                    }
                    $this->conn = $conn;
                } catch (Exception $e) {
                    $this->last_error = $this->messages['db_connect_error'] . ': ' . $e->getMessage();
                    $this->_trigger_error();
                }
            }
        }

        return $this->conn;
    }

    /**
     * Disconnect database (if connection has been established).
     */
    public function db_disconnect()
    {
        $conn = $this->conn;
        if (null !== $conn) {
            if ('MYSQLi' == $this->rdbms) {
                $conn->close();
            }
            if ('POSTGRES' == $this->rdbms) {
                pg_close($conn);
            }
        }
    }

    /**
     * Escape and Quote string to be safe for SQL queries.
     *
     * @param $str
     *
     * @return string
     */
    public function qstr($str)
    {
        $conn = $this->db_connect();
        if (!$conn) {
            return false;
        }
        $res = '';
        switch ($this->rdbms) {
            case 'MYSQLi':
                $res = '\'' . $conn->real_escape_string($str) . '\'';
                break;
            case 'POSTGRES':
                $res = pg_escape_literal($conn, $str);
                break;
        }

        return $res;
    }

    /**
     * Executes a SELECT statement.
     *
     * number of rows returned are available using $this->getNumRows()
     * data result is available using  $this->getData()
     * error is available using  $this->getLastError()
     * error number (if exists) is available using  $this->getLastErrorNumber()
     *
     * @param string $sql
     * @param array  $bind_params
     * @param array  $options
     *
     * @return bool (true on success)
     */
    public function select($sql, $bind_params = [], $options = [])
    {
        return $this->_query($sql, $bind_params, $options);
    }

    /**
     * Executes an INSERT statement.
     *
     * last inserted id is available using $this->getInsertId()
     * affected rows available using $this->getAffectedRows()
     * error is available using  $this->getLastError()
     *
     * @param string $sql
     * @param array  $bind_params
     * @param array  $options
     *
     * @return bool (true on success)
     */
    public function insert($sql, $bind_params = [], $options = [])
    {
        return $this->_query($sql, $bind_params, $options);
    }

    /**
     * Executes an UPDATE statement.
     *
     * affected rows available using $this->getAffectedRows()
     * error is available using  $this->getLastError()
     *
     * @param string $sql
     * @param array  $bind_params
     * @param array  $options
     *
     * @return bool (true on success)
     */
    public function update($sql, $bind_params = [], $options = [])
    {
        return $this->_query($sql, $bind_params, $options);
    }

    /**
     * Executes an DELETE statement.
     *
     * affected rows available using $this->getAffectedRows()
     * error is available using  $this->getLastError()
     *
     * @param string $sql
     * @param array  $bind_params
     * @param array  $options
     *
     * @return bool (true on success)
     */
    public function delete($sql, $bind_params = [], $options = [])
    {
        return $this->_query($sql, $bind_params, $options);
    }

    /**
     * Begin Transaction.
     *
     * @return bool
     */
    public function beginTrans()
    {
        $conn = $this->db_connect();
        if (!$conn) {
            return false;
        }

        switch ($this->rdbms) {
            case 'MYSQLi':
                // switch autocommit status to FALSE. Actually, it starts transaction
                $conn->autocommit(false);
                break;
            case 'POSTGRES':
                pg_query($conn, 'BEGIN');
                break;
        }

        return true;
    }

    /**
     * Commit Transaction.
     *
     * @return bool
     */
    public function commitTrans()
    {
        $conn = $this->db_connect();
        if (!$conn) {
            return false;
        }

        switch ($this->rdbms) {
            case 'MYSQLi':
                $conn->commit();
                $conn->autocommit(true);
                break;
            case 'POSTGRES':
                pg_query($conn, 'COMMIT');
                break;
        }

        return true;
    }

    /**
     * Rollback Transaction.
     *
     * @return bool
     */
    public function rollbackTrans()
    {
        $conn = $this->db_connect();
        if (!$conn) {
            return false;
        }

        switch ($this->rdbms) {
            case 'MYSQLi':
                $conn->rollback();
                $conn->autocommit(true);
                break;
            case 'POSTGRES':
                pg_query($conn, 'ROLLBACK');
                break;
        }

        return true;
    }

    /**
     * Execute one or usually multiple SQL statements.
     *
     * You cannot use prepared statements here.
     * See database documentation for statements which cannot be inside SQL script
     * (e.g. CREATE TABLESPACE in Postgres and more)
     *
     * @param $sql
     *
     * @return bool
     */
    public function execute($sql)
    {
        // initialize ----------------------------------------------------------
        $this->last_error = null;
        $this->last_errno = null;

        // get database connection ---------------------------------------------
        $conn = $this->db_connect();
        if (!$conn) {
            return false;
        }

        // MYSQLi --------------------------------------------------------------
        if ('MYSQLi' == $this->rdbms) {
            $rs = $conn->multi_query($sql);
            if (false === $rs) {
                $this->last_error = $this->messages['wrong_sql'] . '. ' . $conn->error;
                $this->last_errno = $conn->errno;
                $this->_trigger_error($this->error_level);

                return false;
            }
        }

        // POSTGRES ------------------------------------------------------------
        if ('POSTGRES' == $this->rdbms) {
            try {
                $rs = pg_query($conn, $sql);
            } catch (Exception $e) {
                $this->last_error = $this->messages['wrong_sql'] . '. ' . pg_last_error();
                $this->_trigger_error($this->error_level);

                return false;
            }
        }

        return true;
    }

    // PUBLIC FUNCTIONS - UTILITIES --------------------------------------------

    /**
     * SQL lower case.
     *
     * @param mixed $sql_string
     */
    public function lower($sql_string)
    {
        $res = '';
        if ('MYSQLi' == $this->rdbms) {
            $res = 'LOWER(' . $sql_string . ')';
        }
        if ('POSTGRES' == $this->rdbms) {
            $res = 'LOWER(' . $sql_string . ')';
        }

        return $res;
    }

    /**
     * SQL limit.
     *
     * @param $row_count
     * @param $offset
     *
     * @return string
     */
    public function limit($row_count, $offset)
    {
        $res = '';
        if ('MYSQLi' == $this->rdbms) {
            $res = 'LIMIT ' . $row_count . ' OFFSET ' . $offset;
        }
        if ('POSTGRES' == $this->rdbms) {
            $res = 'LIMIT ' . $row_count . ' OFFSET ' . $offset;
        }

        return $res;
    }

    // PUBLIC FUNCTIONS - MEMCACHED --------------------------------------------

    /**
     * Initialize memcached and add server(s) to cache pool.
     *
     * @return object Memcached 'connection'
     */
    public function mc_init()
    {
        if (null === $this->mc) {
            if (extension_loaded('memcached')) {
                $mc_settings = $this->mc_settings;

                $mc_items = 0;
                $mc       = new Memcached();
                foreach ($mc_settings['mc_pool'] as $mc_item) {
                    if (array_key_exists('weight', $mc_item)) {
                        $res_mc = $mc->addServer($mc_item['mc_server'], $mc_item['mc_port'], $mc_item['weight']);
                    } else {
                        $res_mc = $mc->addServer($mc_item['mc_server'], $mc_item['mc_port']);
                    }
                    if ($res_mc) {
                        ++$mc_items;
                    }
                }
                if (0 == $mc_items) {
                    $mc = null;
                }
                $this->mc = $mc;
            }
        }

        return $this->mc;
    }

    /**
     * Pull from memcached.
     *
     * @param string $key the key to search
     *
     * @return mixed the value for key (false if not found)
     */
    public function pull_from_memcached($key)
    {
        $val = false;
        $mc  = $this->mc_init();
        if (null !== $mc) {
            $val = $mc->get($key);
        }

        return $val;
    }

    /**
     * Push to memcached.
     *
     * @param string $key the key to search
     * @param mixed  $val the value of the key
     * @param int    $exp seconds to expire
     *
     * @return array ('code' => ResultCode, 'msg' => ResultMessage)
     */
    public function push_to_memcached($key, $val, $exp = 0)
    {
        $mc = $this->mc_init();
        if (null !== $mc) {
            $mc->set($key, $val, $exp);
        }

        return ['code' => $mc->getResultCode(), 'msg' => $mc->getResultMessage()];
    }

    /**
     * Delete from memcached.
     *
     * @param string $key the key to search
     *
     * @return array ('code' => ResultCode, 'msg' => ResultMessage)
     */
    public function delete_from_memcached($key)
    {
        $mc = $this->mc_init();
        if (null !== $mc) {
            $mc->delete($key);
        }

        return ['code' => $mc->getResultCode(), 'msg' => $mc->getResultMessage()];
    }

    // PRIVATE FUNCTIONS -------------------------------------------------------

    /**
     * @param $sql
     * @param array $bind_params
     * @param array $options
     *
     * @return bool
     */
    private function _query($sql, $bind_params = [], $options = [])
    {
        // get query type
        $a_sql = explode(' ', $sql);
        $mode  = strtolower($a_sql[0]);

        // options -------------------------------------------------------------
        $defaults = [
            'db_name'         => $this->db_name,
            'db_schema'       => $this->db_schema,
            'direct_sql'      => $this->direct_sql,
            'sql_placeholder' => $this->sql_placeholder,
            'use_pst'         => $this->use_pst,
            'pst_placeholder' => $this->pst_placeholder,
            'fetch_type'      => $this->fetch_type, // select
            'get_row'         => false, // select
            'sequence'        => 'auto', // insert
            'error_level'     => $this->error_level,
        ];

        $opt = array_merge($defaults, $options);

        $db_name         = $opt['db_name'];
        $db_schema       = $opt['db_schema'];
        $direct_sql      = $opt['direct_sql'];
        $sql_placeholder = $opt['sql_placeholder'];
        $use_pst         = $opt['use_pst'];
        $pst_placeholder = $opt['pst_placeholder'];
        $fetch_type      = $opt['fetch_type'];
        $get_row         = $opt['get_row'];
        $sequence        = $opt['sequence'];
        $error_level     = $opt['error_level'];

        if ('MYSQLi' == $this->rdbms) {
            $use_prepared_statements = ('select' == $mode ? $use_pst && $bind_params && extension_loaded('mysqlnd') : $use_pst && $bind_params);
        } elseif ('POSTGRES' == $this->rdbms) {
            $use_prepared_statements = $use_pst;
        }

        // initialize ----------------------------------------------------------
        $this->last_error = null;
        $this->last_errno = null;

        if (in_array($mode, ['select'])) {
            $this->data     = null;
            $this->num_rows = null;
            $a_data         = [];
        }
        if (in_array($mode, ['insert'])) {
            $this->insert_id = null;
        }
        if (in_array($mode, ['insert', 'update', 'delete'])) {
            $this->affected_rows = null;
        }

        // get database connection ---------------------------------------------
        $conn = $this->db_connect();
        if (!$conn) {
            return false;
        }

        // sql -----------------------------------------------------------------
        if ($direct_sql) {
            $this->sql = $sql;
        } else {
            $sql_options = [
                'sql_placeholder'         => $sql_placeholder,
                'use_pst'                 => $use_pst,
                'pst_placeholder'         => $pst_placeholder,
                'error_level'             => $error_level,
                'use_prepared_statements' => $use_prepared_statements,
            ];
            $res = $this->_create_sql($sql, $bind_params, $sql_options);
            if (!$res) {
                return false;
            }
        }

        // MYSQLi --------------------------------------------------------------
        if ('MYSQLi' == $this->rdbms) {
            // USE database ----------------------------------------------------
            $rs = $conn->query('USE ' . $db_name);
            if (false === $rs) {
                $this->last_error = $conn->error;
                $this->last_errno = $conn->errno;
                $this->_trigger_error($error_level);

                return false;
            }

            // fetch type (select) ---------------------------------------------
            $a_fetch_type = $this->a_fetch_type_mysql;

            // proceed to query ------------------------------------------------
            if ($use_prepared_statements) {
                // Prepare statement
                $stmt = $conn->prepare($this->sql);
                if (false === $stmt) {
                    $this->last_error = $this->messages['wrong_sql'] . ': ' . $this->sql . '. ' . $conn->error;
                    $this->last_errno = $conn->errno;
                    $this->_trigger_error($error_level);

                    return false;
                }

                // use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array
                $a_params = [];
                $a_types  = $this->a_types;

                $param_type = '';
                $n          = count($bind_params);
                for ($i = 0; $i < $n; ++$i) {
                    $param_type .= $a_types[gettype($bind_params[$i])];
                }

                // with call_user_func_array, array params must be passed by reference
                $a_params[] = &$param_type;

                for ($i = 0; $i < $n; ++$i) {
                    // with call_user_func_array, array params must be passed by reference
                    $a_params[] = &$bind_params[$i];
                }
                call_user_func_array([$stmt, 'bind_param'], $a_params);

                // Execute statement
                $stmt->execute();

                if ($stmt->error) {
                    $this->last_error = $this->messages['query_execution_error'] . ': ' . $stmt->error;
                    $this->last_errno = $stmt->errno;
                    $this->_trigger_error($error_level);

                    return false;
                }

                if (in_array($mode, ['select'])) {
                    // Fetch result to array
                    $rs = $stmt->get_result();

                    $this->num_rows = $rs->num_rows;

                    while ($row = $rs->fetch_array($a_fetch_type[$fetch_type])) {
                        array_push($a_data, $row);
                    }

                    // free result
                    $stmt->free_result();

                    $this->data = $a_data;
                    if ($get_row && $a_data) {
                        $this->data = $a_data[0];
                    }
                }

                if (in_array($mode, ['insert'])) {
                    $this->insert_id = $stmt->insert_id;
                }

                if (in_array($mode, ['insert', 'update', 'delete'])) {
                    $this->affected_rows = $stmt->affected_rows;
                }

                // close statement
                $stmt->close();
            } else {
                $rs = $conn->query($this->sql);
                if (false === $rs) {
                    $this->last_error = $this->messages['wrong_sql'] . ': ' . $this->sql . '. ' . $conn->error;
                    $this->last_errno = $conn->errno;
                    $this->_trigger_error($error_level);

                    return false;
                }

                if (in_array($mode, ['select'])) {
                    $this->num_rows = $rs->num_rows;

                    if ($this->num_rows > 0) {
                        $rs->data_seek(0);
                        if (extension_loaded('mysqlnd')) {
                            // mysqlnd available
                            $a_data = $rs->fetch_all($a_fetch_type[$fetch_type]);
                        } else {
                            while ($row = $rs->fetch_array($a_fetch_type[$fetch_type])) {
                                array_push($a_data, $row);
                            }
                        }
                    }
                    $rs->free();

                    $this->data = $a_data;
                    if ($get_row && $a_data) {
                        $this->data = $a_data[0];
                    }
                }

                if (in_array($mode, ['insert'])) {
                    $this->insert_id = $conn->insert_id;
                }

                if (in_array($mode, ['insert', 'update', 'delete'])) {
                    $this->affected_rows = $conn->affected_rows;
                }
            }
        }

        // POSTGRES ------------------------------------------------------------
        if ('POSTGRES' == $this->rdbms) {
            // SET search_path -------------------------------------------------
            if ($db_schema) {
                $rs = pg_query($conn, 'SET search_path TO ' . $db_schema);

                if (false === $rs) {
                    $this->last_error = pg_last_error();
                    $this->_trigger_error($error_level);

                    return false;
                }
            }
            // fetch type (select) ---------------------------------------------
            $a_fetch_type = $this->a_fetch_type_postgres;

            // proceed to query ------------------------------------------------
            try {
                if ($use_prepared_statements) {
                    $rs = pg_query_params($conn, $this->sql, $bind_params);
                } else {
                    $rs = pg_query($conn, $this->sql);
                }

                if (in_array($mode, ['select'])) {
                    $this->num_rows = pg_num_rows($rs);

                    while ($row = pg_fetch_array($rs, null, $a_fetch_type[$fetch_type])) {
                        array_push($a_data, $row);
                    }

                    $this->data = $a_data;
                    if ($get_row && $a_data) {
                        $this->data = $a_data[0];
                    }
                }

                if (in_array($mode, ['insert'])) {
                    // get last inserted value of serial column
                    if ($sequence) {
                        if ('auto' == $sequence) {
                            $a_sql         = explode(' ', $this->sql);
                            $table_name    = $a_sql[2];
                            $sequence_name = $table_name . '_id_seq';
                        } else {
                            $sequence_name = $sequence;
                        }
                        $sql_serial      = "SELECT currval('$sequence_name')";
                        $rs_serial       = pg_query($conn, $sql_serial);
                        $this->insert_id = pg_fetch_result($rs_serial, 0, 0);
                    }
                }

                if (in_array($mode, ['insert', 'update', 'delete'])) {
                    $this->affected_rows = pg_affected_rows($rs);
                }
            } catch (Exception $e) {
                $this->last_error = $this->messages['wrong_sql'] . ': ' . $this->sql . '. ' . pg_last_error();
                $this->_trigger_error($error_level);

                return false;
            }
        }

        return true;
    }

    /**
     * Create SQL.
     *
     * @param $stmt
     * @param $bind_params
     * @param $options
     *
     * @return bool
     */
    private function _create_sql($stmt, $bind_params, $options)
    {
        // options -------------------------------------------------------------
        $defaults = [
            'sql_placeholder'         => $this->sql_placeholder,
            'use_pst'                 => $this->use_pst,
            'pst_placeholder'         => $this->pst_placeholder,
            'error_level'             => $this->error_level,
            'use_prepared_statements' => null,
        ];

        $opt = array_merge($defaults, $options);

        $sql_placeholder = $opt['sql_placeholder'];
        $use_pst         = $opt['use_pst'];
        $pst_placeholder = $opt['pst_placeholder'];
        if ('auto' == $pst_placeholder) {
            $a_pst_placeholder = $this->a_pst_placeholder;
            $pst_placeholder   = $a_pst_placeholder[$this->rdbms];
        }
        $error_level             = $opt['error_level'];
        $use_prepared_statements = $opt['use_prepared_statements'];

        $this->sql = null;

        $a_stmt = explode($sql_placeholder, $stmt);

        $count_params_st = count($a_stmt) - 1;
        $count_params    = count($bind_params);
        if ($count_params_st != $count_params) {
            $this->last_error = sprintf($this->messages['invalid_number_of_variables'], $count_params, $count_params_st);
            $this->_trigger_error($error_level);

            return false;
        }

        $sql = '';
        if ($use_prepared_statements) {
            if ('question_mark' == $pst_placeholder) {
                $sql = implode('?', $a_stmt);
            } elseif ('numbered' == $pst_placeholder) {
                foreach ($a_stmt as $key => $part) {
                    $idx = $key + 1;
                    $sql .= $part . ($key < $count_params ? '$' . $idx : '');
                }
            }
        } else {
            foreach ($a_stmt as $key => $part) {
                $param = '';
                if ($key < $count_params) {
                    $param = $bind_params[$key];
                    if (null === $bind_params[$key]) {
                        $param = 'NULL';
                    } else {
                        if ('string' == gettype($bind_params[$key])) {
                            $param = $this->qstr($param);
                        }
                    }
                }
                $sql .= $part . $param;
            }
        }

        $this->sql = $sql;

        return true;
    }

    /**
     * Trigger error.
     *
     * @param mixed|null $error_level
     */
    private function _trigger_error($error_level = null)
    {
        if (null === $error_level) {
            $error_level = $this->error_level;
        }
        if ($error_level) {
            trigger_error($this->last_error, $error_level);
        }
    }
}
