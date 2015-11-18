<?php



abstract class Db_Abstract {
    
    /**
     * An array of key/value pairs containing configuration options for 
     * connection to the database.
     *
     * @var array
     */
    protected $_config = array();
    
    /**
     * Database connection
     *
     * @var object|resource|null
     */
    protected $_connection = null;
    
    
    /**
     * Constructor.
     *
     * $config is an array of key/value pairs containing configuration options
     * for connection to database.
     *
     * dbname         => (string) name of the database
     * username       => (string) connect to the database as this username.
     * password       => (string) password associated with the username.
     * host           => (string) what host to connect to
     * 
     * @param array $config
     * 
     */
    final public function __construct(array $config) {
        $this->_setConfig($config);
    }
    
    
    /**
     * Set config options.
     * Throw exceptions if any are missing.
     * 
     * @param array $config
     * @throws Exception
     */
    protected function _setConfig(array $config) {
        if (!array_key_exists('dbname', $config)) {
            throw new Exception("Configuration array must have a key for 'dbname'");
        }

        if (! array_key_exists('password', $config)) {
            throw new Exception("Configuration array must have a key for 'password'");
        }

        if (! array_key_exists('username', $config)) {
            throw new Exception("Configuration array must have a key for 'username'");
        }
        
        if (! array_key_exists('host', $config)) {
            throw new Exception("Configuration array must have a key for 'host'");
        }
        
        $this->_config = $config;
    }
    
    
    /**
     * Open a new connection to the SQL server
     * 
     * Returns object or resource on success or if is already connected.
     * Exception on failure.
     * 
     * @return object|resource|bool
     * @throws Exception
     */
    abstract public function connect();
    
    
    /**
     * Pings a server connection, or tries to reconnect if the connection has 
     * gone down.
     * 
     * Returns TRUE on success or FALSE on failure. 
     * 
     * @return bool
     */
    abstract public function ping();
    
    
    /**
     * Closes a previously opened database connection.
     * 
     * Returns TRUE on success or FALSE on failure.
     * 
     * @return bool
     */
    abstract public function close();
    
    
    /**
     * Returns a string description of the last error
     * 
     * A string that describes the error. An empty string if no error occurred.
     * 
     * @return string
     */
    abstract public function error();
    
    
    /**
     * Returns the error code for the most recent function call
     * 
     * An error code value for the last call, if it failed. 
     * Zero means no error occurred. 
     * 
     * @return int
     */
    abstract public function errno();
    
    /**
     * Performs a query on the database.
     * 
     * Returns FALSE on failure. For successful SELECT, SHOW, DESCRIBE or 
     * EXPLAIN queries will return a result object or resource. 
     * For other successful queries will return TRUE.
     * 
     * @param string $query
     * 
     * @return object|resource|bool
     */
    abstract public function query($query);
    
    
    /**
     * Fetch a result row as an associative, a numeric array, or both.
     * 
     * Returns an array of strings that corresponds to the fetched row or NULL 
     * if there are no more rows in resultset.  
     * 
     * @param object|resource $result
     *      A result returned by self::query();
     * @param int $resultType
     *      This optional parameter is a constant indicating what type of array 
     *      should be produced from the current row data. The possible values 
     *      for this parameter are the constants MYSQL_ASSOC, MYSQL_NUM, or 
     *      MYSQL_BOTH.
     *      
     * @return array|null
     */
    abstract public function fetch($result, $resultType=null);
    
    
    /**
     * Gets the number of affected rows in a previous SQL operation
     * 
     * Returns the number of rows affected by the last INSERT, UPDATE, REPLACE 
     * or DELETE query. 
     * 
     * An integer greater than zero indicates the number of rows affected.
     * Zero indicates that no records where updated. -1 indicates that the 
     * query returned an error.
     * 
     * @return int
     */
    abstract public function affectedRows();
    
    
    /**
     * Returns the auto generated id used in the last query
     * 
     * The value of the AUTO_INCREMENT field that was updated by the previous 
     * query. Returns zero if there was no previous query on the connection or 
     * if the query did not update an AUTO_INCREMENT value.
     * 
     * @return int
     */
    abstract public function insertId();
    
    /**
     * Escapes special characters in a string for use in an SQL statement, 
     * taking into account the current charset of the connection.
     * 
     * Returns an escaped string.
     * 
     * @param string $unescapedString 
     *      The string to be escaped. 
     * 
     * @return string
     */
    abstract public function escape($unescapedString);
    
    
}