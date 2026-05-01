<?php
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../response/response.php';

    class UserProfile {
        public static function getUserProfile($json) {
            $user = json_decode($json,true);
            $userID = $user["userID"];

            try {
                $query = "SELECT * FROM users WHERE user_id = '" . $userID . "'";
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

        public static function updateProfile($json) {
            $user = json_decode($json,true);

            $userID = $user['userID'];
            $name = $user['name'];
            $surname = $user['surname'];
            $username = $user['username'];
            $email = $user['email'];
            $password = $user['password'];

            try {
                $query = "UPDATE users SET 
                name = '" . $name . "',
                surname = '" . $surname . "',
                username = '" . $username . "',
                email = '" . $email . "',
                password = '" . $password . "'
                WHERE user_id = '" . $userID . "'";

                $result = Database::queryDatabase($query);

                if (!$result) {
                    return new Response(false, "Failed to update profile");
                }

                return new Response(true, "Profile updated successfully");

            } catch (Exception $e) {
                return new Response(false, "Error: " . $e -> getMessage());
            }
        }
    }