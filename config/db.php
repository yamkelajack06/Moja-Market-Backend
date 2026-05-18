<?php
require_once 'config.php';

class Database {
    private static $conn;

    public static function connect() {
        if (self::$conn) return self::$conn;
        
        $connString = "host=" . DB_HOST . " port=" . DB_PORT . " dbname=" . DB_NAME . " user=" . DB_USER . " password=" . DB_PASS . " sslmode=require";
        self::$conn = @pg_connect($connString);

        if (!self::$conn) {
            throw new Exception("Connection failed.");
        }

        return self::$conn;
    }

    public static function query(string $sql, array $values = []) {
        try {
            $conn = self::connect();
            $stmtName = 'stmt_' . md5($sql . uniqid());

            $prepare = pg_prepare($conn, $stmtName, $sql);
            if (!$prepare) {
                throw new Exception("Prepare failed: " . pg_last_error($conn));
            }

            $result = pg_execute($conn, $stmtName, $values);
            if (!$result) {
                throw new Exception("Execute failed: " . pg_last_error($conn));
            }

            return $result;

        } catch (Exception $e) {
            throw new Exception("Query error: " . $e->getMessage());
        }
    }
}