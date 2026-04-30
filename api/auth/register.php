<?php
    require __DIR__ . '../../models/user.php';
    require __DIR__ . '../../config/db.php';
    require __DIR__ . '../../response/register.php';
    require __DIR__ . '../../utils/utils.php';

    class Register {
        //The user take in will be a JSON object from the frontend

        public static function registerUser($user) {
            //Assign the variables
            $message = "";
            $userID = $user -> $userID;
            $name = $user -> $name;
            $surname = $user -> surname;
            $username = $user -> username;
            $email = $user -> $email;
            $password = $user -> $password;
            $success = false;

            //check if user is registered to ensure user uniqueness
            try {
                $is_registered = Utility::checkUserRegistered($user -> $userName, $user -> $email);

                //throw error if user is already registered
                if ($is_registered) {
                    throw new ErrorException("User already exists. User a different email and/or username");
                }
            } catch (Exception $e) {
                echo $e -> getMessage();
            }

            //register user if not registered already
            try {
                $query = "INSERT INTO user VALUES ($userID, '$name', '$surname', '$userName', '$email', '$password');";
                $result = Database::queryDatabase($query);

                $message = "New user successfully registered";
                $success = true;

            } catch (Exception $e) {
                $message = $e -> getMessage();
            }

            return new AuthResponse($success,$message);
        }
    }