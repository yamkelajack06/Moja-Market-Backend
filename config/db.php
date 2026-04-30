<?php
require_once 'config.php';

class Database {
    private $conn;

    public function connect() {
        try {
            $connString = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS . " sslmode=require";
            $this->conn = pg_connect($connString);

            if (!$this->conn) {
                throw new Exception("Connection failed: " . pg_last_error());
            }

            echo "Database connection successful";
            return $this->conn;

        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
    }
}