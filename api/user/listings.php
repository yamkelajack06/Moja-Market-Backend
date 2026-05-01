<?php 
    require_once __DIR__ . '/../../config/db.php';

    class UserListings {
        public static function getUserListings($user_id) {
            try {
                $query = "SELECT * FROM item WHERE user_id = '" . $user_id . "'";   
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return null; //listings not found
                }
            
                $rows = pg_fetch_all($result);
                return json_encode($rows);

            } catch (Exception $e) {
                return null; 
            }
        }    
    }