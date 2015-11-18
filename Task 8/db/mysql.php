<?php
require_once 'abstract.php';

class Db_Mysql extends Db_Abstract {

    /**
     * Database connection
     * @var object|resource|null
     */
    protected $conn;

    /**
     * Open a new connection to the SQL server
     * @return object|resource|bool
     * @throws Exception
     */
    public function connect() {

        // Create a database connection
        try
        {
            $this->_connection = new mysqli($this->_config['host'], $this->_config['username'], $this->_config['password'], $this->_config['dbname']);

            // Check connection
            if (!$this->_connection->connect_errno) {
                // Sets the default character set to be used when sending data from and to the database server
                $this->_connection->set_charset("utf8");
                return $this->_connection;
            }
            else
                throw new Exception("Connect failed: ". $this->_connection->connect_error);
        }
        catch(Exception $e)
        {
            echo $e->getMessage();
            return false;
        }
    }

    /**
     * Pings a server connection, or tries to reconnect if the connection has
     * gone down.
     * @return bool
     */
    public function ping() {
        // check if server is alive
        if ($this->_connection->ping())
            return true;
        else {
            // try to reconnect
            if ($this->connect())
                return true;
            else
                return false;
        }
    }

    /**
     * Closes a previously opened database connection.
     * @return bool
     */
    public function close() {

        if($this->_connection->close())
            return true;
        else
            return false;
    }

    /**
     * Returns a string description of the last error
     * @return string
     */
    public function error() {
        //return error_get_last()['message'];
        return $this->_connection->error;
    }

    /**
     * Returns the error code for the most recent function call
     * @return int
     */
    public function errno() {
        return $this->_connection->errno;
    }

    /**
     * Performs a query on the database.
     * @param string $query
     * @return object|resource|bool
     */
    public function query($query) {

        if ($result = $this->_connection->query($query)) {
            return $result;
        }
        return false;
    }

    /**
     * Fetch a result row as an associative, a numeric array, or both.
     * @return array|null
     */
    public function fetch($result, $resultType=null) {
        $row = $result->fetch_array($resultType);

        return $row;
    }

    /**
     * Gets the number of affected rows in a previous SQL operation
     * @return int
     */
    public function affectedRows() {
        return $this->_connection->affected_rows;
    }

    /**
     * Returns the auto generated id used in the last query
     * @return int
     */
    public function insertId() {
        return $this->_connection->insert_id;
    }

    /**
     * Escapes special characters in a string for use in an SQL statement,
     * taking into account the current charset of the connection.
     * @param string $unescapedString
     *      The string to be escaped.
     * @return string
     */
    public function escape($unescapedString) {
        $escaped_string = $this->_connection->real_escape_string($unescapedString);
        return $escaped_string;
    }
    
    
}