<?php

namespace App\Utility;

use \PDO;

class Connection {

    /**
     * database variables
     * @var string
     */
    private $host,
            $database,
            $username,
            $password,
            $conn;

    /**
     * setup the connection
     */
    public function __construct()
    {
        $this->host = env('DB_HOST');
        $this->database = env('DB_DATABASE');
        $this->username = env('DB_USERNAME');
        $this->password = env('DB_PASSWORD');
    }

    /**
     * [query description]
     * @param  string $sql  the sql query to be executed
     * @param  array  $data data to be bound to the query
     * @return [type]       [description]
     */
    public function query($sql, $data = [])
    {
        $this->setConnection();
        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);
        return $stmt;
    }

    /**
     * set the connection
     */
    private function setConnection()
    {
        $this->conn = new PDO('mysql:host='.$this->host.';dbname='.$this->database, $this->username, $this->password);
        $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
}
