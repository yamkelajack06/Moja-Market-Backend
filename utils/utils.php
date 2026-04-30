<?php
    require_once __DIR__ . '/../response/auth.php';
    class Utility {
        //Runs DB query to check if user exists or not for registering
        public static function checkUserExists(string $username, string $email) {
            $is_found = false;
            $query = "SELECT EXISTS (SELECT * FROM users WHERE username = '" . $username . "' OR email = '" . $email . "')";
            $result = Database::queryDatabase($query);

            if ($result != "") {
                $rows = pg_fetch_all($result);
                $exists = $rows[0]['exists'];

                if ($exists == 't') {
                    $is_found = true;
                    return new AuthResponse($is, "User is already registered");
                }

                return new AuthResponse($is_found, "User does not exist");
            }
        }

        //Runs db query to check if user login details match any user in the database
        public static function getUser(string $loginID, string $password) {
            $is_valid = false;
            $query = "SELECT * FROM users WHERE (username = '" . $loginID . "' OR email = '" . $loginID . "') AND password = '" . $password . "'";

            $result = Database::queryDatabase($query);

            if ($result) {
                $rows = pg_fetch_all($result);

                if (empty($rows)) {
                    return null; // user not found
                }

                return json_encode($rows[0]);
            } else {
                throw new ErrorException("Database query failed");
            }
        }
    }