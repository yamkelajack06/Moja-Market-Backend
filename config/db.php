<?php
require_once 'config.php';

class Database {
    private static $conn;
    private static $connString;

    public static function setConnectionString() {
        self::$connString = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS . " sslmode=require";
    }

    public static function connect() {
        try {
            self::setConnectionString();

            self::$conn = @pg_connect(self::$connString);

            if (!self::$conn) {
                throw new Exception("Connection failed.");
            }

            echo "Database connection successful";
            return self::$conn;

        } catch (Exception $e) {
            die("Database error: " . $e->getMessage());
        }
    }

    //fetching data from table
    public static function queryDatabase(string $query) {
        try {
            $result = pg_query(self::$conn, $query);

            if (!$result) {
                throw new Exception(pg_last_error(self::$conn));
            }

            return $result;

        } catch (Exception $e) {
            die("Query error: " . $e->getMessage());
        }
    }
}