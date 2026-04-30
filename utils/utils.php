<?php
    class Utility {
        //Runs DB query to check if user exists or not for registering
        public static function checkUserExists(string $email, string $username) {
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

        //Runs db query to check if user login details match any user in the database
        public static function getUser(string $loginID, string $password) {
            $is_valid = false;
            $query = "SELECT * FROM user WHERE (username = '$loginID' OR email = '$loginID') AND password = '$password'";

            $result = Database::queryDatabase($query);

            if ($result) {
                $rows = pg_fetch_all($result);
                $res = json_encode($rows[0]);
                return $res;
            } else {
                throw ErrorException("Database query failed");
            }
        }
    }