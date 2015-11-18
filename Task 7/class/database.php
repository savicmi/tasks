<?php
/**
 * User: Milos Savic
 * Database configuration
 */

class database {

    private $servername; // Host name
    private $username; // MySQL user name
    private $password; // MySQL password
    private $dbname; // Database name

    public $conn;

    /**
     * database constructor
     * @param $servername
     * @param $username
     * @param $password
     * @param $dbname
     */
    public function __construct($servername, $username, $password, $dbname) {
        $this->servername = $servername;
        $this->username = $username;
        $this->password = $password;
        $this->dbname = $dbname;

        // Create database connection
        $this->conn = new mysqli($this->servername, $this->username, $this->password, $this->dbname);

        // Check connection
        if ($this->conn->connect_error)
            die("Connection failed: " . $this->conn->connect_error);

        // Sets the default character set to be used when sending data from and to the database server
        $this->conn->set_charset("utf8");
    }

}