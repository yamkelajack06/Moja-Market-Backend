<?php 
    require_once __DIR__ . '/../../config/db.php';

    class UserListings {
        public static function getUserListings($user_id) {
            try {
                $result = Database::query("SELECT * FROM item WHERE user_id = $1", [$user_id]);

                if (!$result) {
                    return new Response(false, "No listings found"); //listings not found
                }
            
                $rows = pg_fetch_all($result);
                return new Response(true, "Listings found", $rows);

            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }
        }    
    }