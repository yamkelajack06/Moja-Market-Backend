<?php
    require __DIR__ . '../../models/user.php';
    require __DIR__ . '../../config/db.php';
    require __DIR__ . '../../response/register.php';

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
                $is_registered = $this -> checkUserRegistered($user -> $userName, $user -> $email);

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

            return new RegisterResponse($success,$message);
        }

        //Runs DB query to check if user exists or not
        public static function checkUserRegistered(string $email, string $username) {
            $is_found = false;
            $query = "SELECT EXISTS (SELECT * FROM user WHERE username = '$username' OR email = '$email');";
            $result = Database::queryDatabase($query);

            //Handle database query responses
            if ($result) {
                $rows = pg_fetch_all($result);
                $res = json_encode($rows[0]);

                if ($res == 't') {
                    $is_found = true;
                }
            } else {
                throw ErrorException("Database query failed");
            }

            return $is_found;
        }
    }