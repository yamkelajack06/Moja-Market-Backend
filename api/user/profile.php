<?php
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../response/response.php';
    require_once __DIR__ . '/../../utils/utils.php';

    class UserProfile {
        public static function getUserProfile($json) {
            $user   = json_decode($json, true);
            $userID = $user["userID"];

            try {
                $result = Database::query(
                    "SELECT user_id, name, surname, username, email, createdAt FROM users WHERE user_id = $1",
                    [$userID]
                );

                if (!$result) {
                    return new Response(false, "User not found");
                }

                $rows = pg_fetch_all($result);
                return new Response(true, "User found", $rows[0]);

            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }
        }

        public static function updateProfile($json) {
            $user     = json_decode($json, true);
            $userID   = $user['userID'];
            $name     = $user['name'];
            $surname  = $user['surname'];
            $username = $user['username'];
            $email    = $user['email'];
            $password = $user['password'];

            try {
                $check = Utility::checkUsernameOrEmailTaken($username, $email, $userID);
                if (!$check->getSuccess()) {
                    return $check;
                }

                // Hash the incoming password
                $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                $result = Database::query(
                    "UPDATE users SET name = $1, surname = $2, username = $3, email = $4, password = $5 WHERE user_id = $6",
                    [$name, $surname, $username, $email, $hashedPassword, $userID]
                );

                if (!$result) {
                    return new Response(false, "Failed to update profile");
                }

                return new Response(true, "Profile updated successfully");

            } catch (Exception $e) {
                return new Response(false, "Error: " . $e->getMessage());
            }
        }
    }