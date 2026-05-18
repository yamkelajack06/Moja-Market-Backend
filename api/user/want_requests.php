<?php
    require_once __DIR__ . '/../../config/db.php';

    class WantRequest {
        public static function getUserWantRequests($userID) {
            try {
                $result = Database::query("SELECT * FROM wantrequest WHERE user_id = $1", [$userID]);

                if (!$result) {
                    return new Response(false, "No want requests found"); //want requests not found
                }

                $rows = pg_fetch_all($result);
                return new Response(true, "Want requests found", $rows);

            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }
        }
    }