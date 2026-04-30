<?php
    require_once __DIR__ . '/../../models/user.php';
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../response/auth.php';
    require_once __DIR__ . '/../../utils/utils.php';

    class Register {
        //The user take in will be a JSON object from the frontend

        public static function registerUser($json) {
            //convert the json string to an associative array
            $user = json_decode($json, true);

            //Assign the variables from the json
            $message  = "";
            $userID   = $user['userID'];
            $name     = $user['name'];
            $surname  = $user['surname'];
            $username = $user['username'];
            $email    = $user['email'];
            $password = $user['password'];
            $success = false;

            //check if user is registered to ensure user uniqueness
            try {
                $is_registered = Utility::checkUserExists($username,$email);

                //throw error if user is already registered
                if ($is_registered) {
                    throw new ErrorException("User already exists. User a different email and/or username");
                }
            } catch (Exception $e) {
                echo $e -> getMessage();
            }

            //register user if not registered already
            try {
                $query = "INSERT INTO users (userID, name, surname, username, email, password) VALUES ('" . $userID . "', '" . $name . "', '" . $surname . "', '" . $username . "', '" . $email . "', '" . $password . "')";
                $result = Database::queryDatabase($query);

                $message = "New user successfully registered";
                $success = true;

            } catch (Exception $e) {
                $message = $e -> getMessage();
            }

            return new AuthResponse($success,$message);
        }
    }