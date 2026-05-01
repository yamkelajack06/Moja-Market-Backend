<?php
    require_once __DIR__ . '/../../config/db.php';

    class WantRequest {
        public static function getUserWantRequests($userID) {
            try {
                $query = "SELECT * FROM wantrequest WHERE user_id = '" . $userID . "'";
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return null; //want requests not found
                }

                $rows = pg_fetch_all($result);
                return json_encode($rows);

            } catch (Exception $e) {
                return null;
            }
        }
    }