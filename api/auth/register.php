<?php
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../response/response.php';
    require_once __DIR__ . '/../../utils/utils.php';

    class Register {
        public static function registerUser($json) {
            $user = json_decode($json, true);

            $message  = "";
            $userID   = $user['userID'];
            $name     = $user['name'];
            $surname  = $user['surname'];
            $username = $user['username'];
            $email    = $user['email'];
            $password = $user['password'];
            $success  = false;

            try {
                $is_registered = Utility::checkUserExists($username, $email)->getSuccess();

                if ($is_registered) {
                    return new Response(false, "User already exists. Use a different email and/or username");
                }
            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }

            // Hash the password before storing
            $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

            try {
                $result = Database::query(
                    "INSERT INTO users (user_id, name, surname, username, email, password, createdAt) VALUES ($1, $2, $3, $4, $5, $6, NOW())",
                    [$userID, $name, $surname, $username, $email, $hashedPassword]
                );
            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }

            return new Response(true, "New user successfully registered");
        }
    }