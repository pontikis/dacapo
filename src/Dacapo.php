<?php

declare(strict_types=1);

namespace Pontikis\Database;

use ErrorException;
use Exception;

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
 * @license    http://opensource.org/licenses/MIT
 *
 * @version    1.0.0 (XX Jun 2018)
 */
class Dacapo
{
    const DEFAULT_SQL_PLACEHOLDER = '?';

    const RDBMS_MYSQLI   = 'MYSQLi';
    const RDBMS_POSTGRES = 'POSTGRES';

    const SELECT_QUERY = 'select';
    const UPDATE_QUERY = 'update';
    const INSERT_QUERY = 'insert';
    const DELETE_QUERY = 'delete';

    const PREPARED_STATEMENTS_QUESTION_MARK = 'question_mark';
    const PREPARED_STATEMENTS_NUMBERED      = 'numbered';

    const PG_SEQUENCE_NAME_AUTO = 'auto';

    const ERROR_EXCEPTION_IDENTIFIER = 'Dacapo_ErrorException';

    const ERROR_RDBMS_NOT_SUPPORTED = 'Database not supported';
    const ERROR_MYSQLI_IS_REQUIRED  = 'mysqli extension is required';
    const ERROR_MYSQLND_IS_REQUIRED = 'mysqlnd extension is required';
    const ERROR_PGSQL_IS_REQUIRED   = 'pgsql extension is required';

    const ERROR_DBSERVER_IS_REQUIRED = 'Database server name or IP is required';
    const ERROR_DBNAME_IS_REQUIRED   = 'Database name is required';
    const ERROR_DBUSER_IS_REQUIRED   = 'Database user is required';
    const ERROR_DBPASSWD_IS_REQUIRED = 'Database password is required';

    const ERROR_UNSUPPORTED_QUERY = 'Unsupported query type';
    const ERROR_FETCH_ROW_INVALID = 'Query does not return one row';

    // error handler -----------------------------------------------------------
    private $use_dacapo_error_handler;

    // connection params -------------------------------------------------------
    private $conn;

    private $rdbms;
    private $db_server;
    private $db_user;
    private $db_passwd;
    private $db_name;

    // optional
    private $db_port;
    private $charset;
    private $pg_connect_force_new;
    private $pg_connect_timeout;

    // query params ------------------------------------------------------------
    /** @var string supported types SELECT, UPDATE, INSERT, DELETE */
    private $query_type;
    /** @var string Postgres schema */
    private $db_schema;
    /** @var string variables placeholder in SQL statements */
    private $sql_placeholder;
    /** @var string type of placeholder in prepared statements: one of 'question_mark', 'numbered' */
    private $pst_placeholder;
    /** @var int fetch type in various RDBMS: ASSOC, NUM, BOTH */
    private $fetch_type;
    /** @var string the current sql statement for supported query types */
    private $sql;
    /** @var array types of params to bind with MYSQLi */
    private $a_types;
    /** @var array|null data returned */
    private $data;
    /** @var int number of rows returned */
    private $num_rows;
    /** @var bool fetch single row */
    private $fetch_row;
    /** @var int|null last inserted id */
    private $insert_id;
    /** @var string Postgres sequence in INSERT statement (default is 'auto' seq name will be created as tableName_seq_id, null means that there is NO sequence for this table, otherwise the provided name will be used) */
    private $pg_insert_sequence;
    /** @var int number of affected rows in UPDATE, INSERT, DELETE statements */
    private $affected_rows;

    // memcached params --------------------------------------------------------
    private $mc_settings;
    private $mc;

    /**
     * Constructor.
     *
     * @param array $a_db database settings
     * @param array $a_mc memcached settings
     *
     * @throws Exception
     */
    public function __construct(array $a_db, array $a_mc)
    {
        // error handler -------------------------------------------------------
        $this->use_dacapo_error_handler = true;

        // connection params ---------------------------------------------------
        $this->rdbms = $a_db['rdbms'];

        // RDBMS not supported
        if (false === in_array($this->rdbms, [self::RDBMS_MYSQLI, self::RDBMS_POSTGRES])) {
            throw new Exception(self::ERROR_RDBMS_NOT_SUPPORTED);
        }

        // Extension needed
        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                if (false === extension_loaded('mysqli')) {
                    throw new Exception(self::ERROR_MYSQLI_IS_REQUIRED);
                }

                if (false === extension_loaded('mysqlnd')) {
                    throw new Exception(self::ERROR_MYSQLND_IS_REQUIRED);
                }
                break;
            case self::RDBMS_POSTGRES:
                if (false === extension_loaded('pgsql')) {
                    throw new Exception(self::ERROR_PGSQL_IS_REQUIRED);
                }
                break;
        }

        $this->conn = null;

        $this->db_server = array_key_exists('db_server', $a_db) ? $a_db['db_server'] : null;
        $this->db_name   = array_key_exists('db_name', $a_db) ? $a_db['db_name'] : null;
        $this->db_user   = array_key_exists('db_user', $a_db) ? $a_db['db_user'] : null;
        $this->db_passwd = array_key_exists('db_passwd', $a_db) ? $a_db['db_passwd'] : null;

        if (null === $this->db_server) {
            throw new Exception(self::ERROR_DBSERVER_IS_REQUIRED);
        }

        if (null === $this->db_name) {
            throw new Exception(self::ERROR_DBNAME_IS_REQUIRED);
        }

        if (null === $this->db_user) {
            throw new Exception(self::ERROR_DBUSER_IS_REQUIRED);
        }

        if (null === $this->db_passwd) {
            throw new Exception(self::ERROR_DBPASSWD_IS_REQUIRED);
        }

        // optional connection params
        $this->db_port              = null;
        $this->charset              = null;
        $this->pg_connect_timeout   = null;
        $this->pg_connect_force_new = false;

        // query params --------------------------------------------------------
        $this->query_type      = null;
        $this->db_schema       = null;
        $this->sql_placeholder = self::DEFAULT_SQL_PLACEHOLDER;

        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                $this->pst_placeholder = self::PREPARED_STATEMENTS_QUESTION_MARK;
                $this->fetch_type      = MYSQLI_ASSOC;
                break;
            case self::RDBMS_POSTGRES:
                $this->pst_placeholder = self::PREPARED_STATEMENTS_NUMBERED;
                $this->fetch_type      = PGSQL_ASSOC;
                break;
        }

        $this->sql = null;

        // Bind parameters. Types: s = string, i = integer, d = double,  b = blob
        $this->a_types = [
            'string'  => 's',
            'integer' => 'i',
            'double'  => 'd',
            'boolean' => 'i', // avoid boolean in params, use integer instead (1,0)
            'NULL'    => 's', // do not need to cast null to a particular data type
        ];

        $this->data               = null;
        $this->num_rows           = null;
        $this->fetch_row          = false;
        $this->insert_id          = null;
        $this->affected_rows      = null;
        $this->pg_insert_sequence = self::PG_SEQUENCE_NAME_AUTO;

        // memcached params ----------------------------------------------------
        $this->mc_settings = $a_mc;
        $this->mc          = null;
    }

    // error handler -----------------------------------------------------------
    public function getUseDacapoErrorHandler()
    {
        return $this->use_dacapo_error_handler;
    }

    public function setUseDacapoErrorHandler(bool $flag)
    {
        $this->use_dacapo_error_handler = $flag;

        return $this;
    }

    // connection --------------------------------------------------------------
    public function getDbPort()
    {
        return $this->db_port;
    }

    public function setDbPort(int $port)
    {
        $this->db_port = $port;

        return $this;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;

        return $this;
    }

    public function getPgConnectForceNew()
    {
        return $this->pg_connect_force_new;
    }

    public function setPgConnectForceNew(bool $flag)
    {
        $this->pg_connect_force_new = $flag;

        return $this;
    }

    public function getPgConnectTimout()
    {
        return $this->pg_connect_timeout;
    }

    public function setPgConnectTimout($seconds)
    {
        $this->pg_connect_timeout = $seconds;

        return $this;
    }

    // query -------------------------------------------------------------------
    public function getDbSchema()
    {
        return $this->db_schema;
    }

    public function setDbSchema($schema)
    {
        $this->db_schema = $schema;

        return $this;
    }

    public function getSqlPlaceholder()
    {
        return $this->sql_placeholder;
    }

    public function setSqlPlaceholder(string $str)
    {
        $this->sql_placeholder = $str;

        return $this;
    }

    public function getFetchType()
    {
        return $this->fetch_type;
    }

    public function setFetchTypeAssoc()
    {
        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                $this->fetch_type = MYSQLI_ASSOC;
                break;
            case self::RDBMS_POSTGRES:
                $this->fetch_type = PGSQL_ASSOC;
                break;
        }

        return $this;
    }

    public function setFetchTypeNum()
    {
        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                $this->fetch_type = MYSQLI_NUM;
                break;
            case self::RDBMS_POSTGRES:
                $this->fetch_type = PGSQL_NUM;
                break;
        }

        return $this;
    }

    public function setFetchTypeBoth()
    {
        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                $this->fetch_type = MYSQLI_BOTH;
                break;
            case self::RDBMS_POSTGRES:
                $this->fetch_type = PGSQL_BOTH;
                break;
        }

        return $this;
    }

    public function getSQL()
    {
        return $this->sql;
    }

    /**
     * Get data returned from a SELECT query.
     *
     * @return array|null
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Get returned rows count from a SELECT query.
     */
    public function getNumRows()
    {
        return $this->num_rows;
    }

    public function getFetchRow(): bool
    {
        return $this->fetch_row;
    }

    public function setFetchRow(bool $flag)
    {
        $this->fetch_row = $flag;

        return $this;
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
     * Get affected rows count (UPDATE, INSERT, DELETE query).
     *
     * @return int|null
     */
    public function getAffectedRows()
    {
        return $this->affected_rows;
    }

    public function getPgInsertSequence()
    {
        return $this->pg_insert_sequence;
    }

    /**
     * Postgres sequence in INSERT statement
     * default is 'auto' (self::PG_SEQUENCE_NAME_AUTO), seq name will be created as tableName_seq_id,
     * null means that there is NO sequence for this table,
     * otherwise the provided name will be used.
     *
     * @param string|null $seq_name
     */
    public function setPgInsertSequence($seq_name)
    {
        $this->pg_insert_sequence = $seq_name;

        return $this;
    }

    /**
     * Establish database connection.
     *
     * @throws DacapoErrorException
     *
     * @return mysqli|resource
     */
    public function dbConnect()
    {
        if (null === $this->conn) {
            $this->applyDacapoErrorHandler();

            switch ($this->rdbms) {
                case self::RDBMS_MYSQLI:
                    if ($this->db_port) {
                        $conn = new \mysqli(
                            $this->db_server,
                            $this->db_user,
                            $this->db_passwd,
                            $this->db_name,
                            (int) $this->db_port
                        );
                    } else {
                        $conn = new \mysqli(
                            $this->db_server,
                            $this->db_user,
                            $this->db_passwd,
                            $this->db_name
                        );
                    }

                    if ($this->charset) {
                        $conn->set_charset($this->charset);
                    }

                    $this->conn = $conn;
                    break;
                case self::RDBMS_POSTGRES:
                    $dsn = 'host=' . $this->db_server . ' ';

                    if ($this->db_port) {
                        $dsn .= 'port=' . $this->db_port . ' ';
                    }

                    $dsn .= 'dbname=' . $this->db_name . ' ';
                    $dsn .= 'user=' . $this->db_user . ' ';
                    $dsn .= 'password=' . $this->db_passwd;

                    if ($this->pg_connect_timeout) {
                        $dsn .= ' connect_timeout=' . $this->pg_connect_timeout;
                    }

                    if ($this->pg_connect_force_new) {
                        $conn = pg_connect($dsn, PGSQL_CONNECT_FORCE_NEW);
                    } else {
                        $conn = pg_connect($dsn);
                    }

                    if ($this->charset) {
                        pg_set_client_encoding($conn, $this->charset);
                    }

                    $this->conn = $conn;
                    break;
            }

            $this->restoreErrorHandler();
        }

        return $this->conn;
    }

    /**
     * Disconnect database (if connection has been established).
     *
     * @throws DacapoErrorException
     */
    public function dbDisconnect()
    {
        $conn = $this->conn;

        if (null !== $conn) {
            $this->applyDacapoErrorHandler();

            switch ($this->rdbms) {
                case self::RDBMS_MYSQLI:
                    $conn->close();
                    break;
                case self::RDBMS_POSTGRES:
                    pg_close($conn);
                    break;
            }

            $this->restoreErrorHandler();
        }
    }

    /**
     * Executes a SELECT statement.
     *
     * number of rows returned are available using $this->getNumRows()
     * data result is available using  $this->getData()
     *
     * @param string $sql
     * @param array  $bind_params
     *
     * @throws DacapoErrorException
     */
    public function select(
        string $sql,
        array $bind_params = []
    ) {
        $this->dacapoQuery($sql, $bind_params);
    }

    /**
     * Executes an INSERT statement.
     *
     * last inserted id is available using $this->getInsertId()
     * affected rows available using $this->getAffectedRows()
     *
     * @param string $sql
     * @param array  $bind_params
     *
     * @throws DacapoErrorException
     */
    public function insert(
        string $sql,
        array $bind_params = []
    ) {
        $this->dacapoQuery($sql, $bind_params);
    }

    /**
     * Executes an UPDATE statement.
     *
     * affected rows available using $this->getAffectedRows()
     *
     * @param string $sql
     * @param array  $bind_params
     *
     * @throws DacapoErrorException
     */
    public function update(
        string $sql,
        array $bind_params = []
    ) {
        $this->dacapoQuery($sql, $bind_params);
    }

    /**
     * Executes a DELETE statement.
     *
     * affected rows available using $this->getAffectedRows()
     *
     * @param string $sql
     * @param array  $bind_params
     *
     * @throws DacapoErrorException
     */
    public function delete(
        string $sql,
        array $bind_params = []
    ) {
        $this->dacapoQuery($sql, $bind_params);
    }

    /**
     * Begin Transaction.
     */
    public function beginTrans()
    {
        $conn = $this->dbConnect();

        $this->applyDacapoErrorHandler();

        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                // switch autocommit status to FALSE. Actually, it starts transaction
                $conn->autocommit(false);
                break;
            case self::RDBMS_POSTGRES:
                pg_query($conn, 'BEGIN');
                break;
        }
        
        $this->restoreErrorHandler();
    }

    /**
     * Commit Transaction.
     */
    public function commitTrans()
    {
        $conn = $this->dbConnect();
        
        $this->applyDacapoErrorHandler();

        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                $conn->commit();
                $conn->autocommit(true);
                break;
            case self::RDBMS_POSTGRES:
                pg_query($conn, 'COMMIT');
                break;
        }

        $this->restoreErrorHandler();        
    }

    /**
     * Rollback Transaction.
     */
    public function rollbackTrans()
    {
        $conn = $this->dbConnect();
        
        $this->applyDacapoErrorHandler();

        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                $conn->rollback();
                $conn->autocommit(true);
                break;
            case self::RDBMS_POSTGRES:
                pg_query($conn, 'ROLLBACK');
                break;
        }

        $this->restoreErrorHandler();        
    }

    /**
     * Execute one or usually multiple SQL statements.
     *
     * You cannot use prepared statements here.
     * See database documentation for statements which cannot be inside SQL script
     * (e.g. CREATE TABLESPACE in Postgres and more)
     *
     * @param $sql
     */
    public function execute(string $sql)
    {
        // get database connection ---------------------------------------------
        $conn = $this->dbConnect();
        
        $this->applyDacapoErrorHandler();

        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                $conn->multi_query($sql);
                break;
            case self::RDBMS_POSTGRES:
                pg_query($conn, $sql);
                break;
        }
        
        $this->restoreErrorHandler();           
    }

    /**
     * SQL lower case.
     *
     * @param string $sql_string
     */
    public function lower(string $sql_string)
    {
        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                return 'LOWER(' . $sql_string . ')';
            case self::RDBMS_POSTGRES:
                return 'LOWER(' . $sql_string . ')';
        }
    }

    /**
     * SQL limit.
     *
     * @param int $row_count
     * @param int $offset
     *
     * @return string
     */
    public function limit(int $row_count, int $offset)
    {
        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                return 'LIMIT ' . $row_count . ' OFFSET ' . $offset;
            case self::RDBMS_POSTGRES:
                return 'LIMIT ' . $row_count . ' OFFSET ' . $offset;
        }
    }

    // memcached ---------------------------------------------------------------

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
                $mc       = new \Memcached();
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

    /**
     * Error handler function. Replaces PHP's error handler.
     * The only reason to use this error_handler is to convert php errors (usually E_WARNING)
     * from mysqli and pgsql php extension.
     * If your own error_handler already converts E_WARNING to ErrorException you don't need it
     * In this case use setUseDacapoErrorHandler(false).
     *
     * @param int    $err_no
     * @param string $err_str
     * @param string $err_file
     * @param int    $err_line
     *
     * @throws ErrorException
     */
    public function dacapoErrorHandler(
        int $err_no,
        string $err_str,
        string $err_file,
        int $err_line
    ) {
        // if error_reporting is set to 0, exit. This is also the case when using @
        if (!error_reporting()) {
            return;
        }

        // throw ErrorException
        $message = self::ERROR_EXCEPTION_IDENTIFIER . ' ' .
        'ErrNo=' . $err_no . ' (' . $this->getFriendlyErrorType($err_no) . ') ' . $err_str;
        try {
            throw new ErrorException(
                $message,
                $err_no,
                $err_no,
                $err_file,
                $err_line
            );
        } catch (ErrorException $e) {
            restore_error_handler();
            throw new DacapoErrorException(
                $message,
                $err_no,
                $err_no,
                $err_file,
                $err_line,
                $e
            );
        }
    }

    /**
     * Executes a single (suppotred) query.
     *
     * @param string $sql
     * @param array  $bind_params
     *
     * @throws DacapoErrorException
     */
    private function dacapoQuery(
        string $sql,
        array $bind_params = []
    ) {
        $this->applyDacapoErrorHandler();

        $this->sql           = null;
        $this->data          = null;
        $this->num_rows      = null;
        $this->insert_id     = null;
        $this->affected_rows = null;
        $a_data              = [];

        // get bind params count
        $bind_params_count = count($bind_params);

        // get query type
        $a_sql            = explode(' ', trim($sql));
        $this->query_type = strtolower($a_sql[0]);

        if (!in_array($this->query_type,
            [
                self::SELECT_QUERY,
                self::UPDATE_QUERY,
                self::INSERT_QUERY,
                self::DELETE_QUERY,
            ])) {
            trigger_error(self::ERROR_UNSUPPORTED_QUERY, E_WARNING);
        }

        // get database connection ---------------------------------------------
        $conn = $this->dbConnect();

        // construct sql -------------------------------------------------------
        $a_stmt = explode($this->sql_placeholder, $sql);

        switch ($this->pst_placeholder) {
            case self::PREPARED_STATEMENTS_QUESTION_MARK:
                $this->sql = implode('?', $a_stmt);
                break;
            case self::PREPARED_STATEMENTS_NUMBERED:
                $this->sql = '';
                foreach ($a_stmt as $key => $part) {
                    $idx = $key + 1;
                    $this->sql .= $part . ($key < $bind_params_count ? '$' . $idx : '');
                }
                break;
        }

        switch ($this->rdbms) {
            case self::RDBMS_MYSQLI:
                // USE database
                $conn->query('USE ' . $this->db_name);

                // Prepare statement
                $stmt = $conn->prepare($this->sql);
                if (false === $stmt) {
                    trigger_error($conn->error, E_WARNING);
                }

                if ($bind_params_count > 0) {
                    // use call_user_func_array, as $stmt->bind_param('s', $param); does not accept params array
                    $a_params = [];
                    $a_types  = $this->a_types;

                    $param_type = '';
                    for ($i = 0; $i < $bind_params_count; ++$i) {
                        $param_type .= $a_types[gettype($bind_params[$i])];
                    }

                    // with call_user_func_array, array params must be passed by reference
                    $a_params[] = &$param_type;

                    for ($i = 0; $i < $bind_params_count; ++$i) {
                        // with call_user_func_array, array params must be passed by reference
                        $a_params[] = &$bind_params[$i];
                    }
                    call_user_func_array([$stmt, 'bind_param'], $a_params);
                }

                // Execute statement
                $stmt->execute();

                if (self::SELECT_QUERY === $this->query_type) {
                    // Fetch result to array
                    $rs = $stmt->get_result();

                    $this->num_rows = $rs->num_rows;

                    while ($row = $rs->fetch_array($this->fetch_type)) {
                        $a_data[] = $row;
                    }

                    // free result
                    $stmt->free_result();

                    $this->data = $a_data;
                    if ($this->fetch_row) {
                        if (1 === count($a_data)) {
                            $this->data = $a_data[0];
                        } else {
                            trigger_error(self::ERROR_FETCH_ROW_INVALID, E_WARNING);
                        }
                    }
                }

                if (in_array($this->query_type,
                    [
                        self::UPDATE_QUERY,
                        self::INSERT_QUERY,
                        self::DELETE_QUERY,
                    ])) {
                    $this->affected_rows = $stmt->affected_rows;
                }

                if (self::INSERT_QUERY === $this->query_type) {
                    $this->insert_id = $stmt->insert_id;
                }

                // Close statement
                $stmt->close();

                break;
            case self::RDBMS_POSTGRES:
                // SET search_path ---------------------------------------------
                if ($this->db_schema) {
                    pg_query($conn, 'SET search_path TO ' . $this->db_schema);
                }

                // proceed to query
                $rs = pg_query_params($conn, $this->sql, $bind_params);

                if (self::SELECT_QUERY === $this->query_type) {
                    $this->num_rows = pg_num_rows($rs);

                    while ($row = pg_fetch_array($rs, null, $this->fetch_type)) {
                        array_push($a_data, $row);
                    }

                    $this->data = $a_data;
                    if ($this->fetch_row) {
                        if (1 === count($a_data)) {
                            $this->data = $a_data[0];
                        } else {
                            trigger_error(self::ERROR_FETCH_ROW_INVALID, E_WARNING);
                        }
                    }
                }

                if (in_array($this->query_type,
                    [
                        self::UPDATE_QUERY,
                        self::INSERT_QUERY,
                        self::DELETE_QUERY,
                    ])) {
                    $this->affected_rows = pg_affected_rows($rs);
                }

                if (self::INSERT_QUERY === $this->query_type) {
                    if (null !== $this->pg_insert_sequence) {
                        // get last inserted value of serial column
                        if (self::PG_SEQUENCE_NAME_AUTO === $this->pg_insert_sequence) {
                            $table_name    = $a_sql[2];
                            $sequence_name = $table_name . '_id_seq';
                        } else {
                            $sequence_name = $this->pg_insert_sequence;
                        }

                        $sql_serial      = "SELECT currval('$sequence_name')";
                        $rs_serial       = pg_query($conn, $sql_serial);
                        $this->insert_id = pg_fetch_result($rs_serial, 0, 0);
                    }
                }

                break;
        }

        $this->restoreErrorHandler();
    }

    private function applyDacapoErrorHandler()
    {
        if (true === $this->use_dacapo_error_handler) {
            set_error_handler([$this, 'dacapoErrorHandler'], E_ALL);
        }
    }

    private function restoreErrorHandler()
    {
        if (true === $this->use_dacapo_error_handler) {
            restore_error_handler();
        }
    }

    /**
     * @param int $type
     *
     * @return string
     */
    private function getFriendlyErrorType(int $type)
    {
        switch ($type) {
            case E_ERROR: // 1 //
                return 'E_ERROR';
            case E_WARNING: // 2 //
                return 'E_WARNING';
            case E_PARSE: // 4 //
                return 'E_PARSE';
            case E_NOTICE: // 8 //
                return 'E_NOTICE';
            case E_CORE_ERROR: // 16 //
                return 'E_CORE_ERROR';
            case E_CORE_WARNING: // 32 //
                return 'E_CORE_WARNING';
            case E_COMPILE_ERROR: // 64 //
                return 'E_COMPILE_ERROR';
            case E_COMPILE_WARNING: // 128 //
                return 'E_COMPILE_WARNING';
            case E_USER_ERROR: // 256 //
                return 'E_USER_ERROR';
            case E_USER_WARNING: // 512 //
                return 'E_USER_WARNING';
            case E_USER_NOTICE: // 1024 //
                return 'E_USER_NOTICE';
            case E_STRICT: // 2048 //
                return 'E_STRICT';
            case E_RECOVERABLE_ERROR: // 4096 //
                return 'E_RECOVERABLE_ERROR';
            case E_DEPRECATED: // 8192 //
                return 'E_DEPRECATED';
            case E_USER_DEPRECATED: // 16384 //
                return 'E_USER_DEPRECATED';
        }

        return 'UNKNOWN ERROR';
    }
}
