<?php
    require_once __DIR__ . '/../config/db.php';

    class UserProfile {
        public static function getUserProfile($userID) {
            try {
                $query = "SELECT 1 FROM users WHERE ". $userID . "";
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return null;
                }

                $rows = pg_fetch_all($result);
                return json_encode($rows);

            } catch (Exception $e) {
                return null;
            }
        }
    }