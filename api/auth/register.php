<?php
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../response/response.php';
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
                $is_registered = Utility::checkUserExists($username,$email)-> getSuccess();

                //throw error if user is already registered
                if ($is_registered) {
                    $message = "User already exists. User a different email and/or username";
                    return new Response($success,$message);
                }
            } catch (Exception $e) {
                $message = $e -> getMessage();
                return new Response($success,$message);
            }

            //register user if not registered already
            try {
                $query = "INSERT INTO users (user_id, name, surname, username, email, password) VALUES ('" . $userID . "', '" . $name . "', '" . $surname . "', '" . $username . "', '" . $email . "', '" . $password . "')";
                $result = Database::queryDatabase($query);

                $message = "New user successfully registered";
                $success = true;

            } catch (Exception $e) {
                $message = $e -> getMessage();
                return new Response($success,$message);
            }

            return new Response($success,$message);
        }
    }