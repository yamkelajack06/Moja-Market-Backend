<?php
    require_once __DIR__ . '/../response/response.php';
    class Utility {
        //Runs DB query to check if user exists or not for registering
        public static function checkUserExists(string $username, string $email) {
            $result = Database::query("SELECT EXISTS (SELECT 1 FROM users WHERE username = $1 OR email = $2)", [$username, $email]);

            if ($result != "") {
                $rows = pg_fetch_all($result);
                $exists = $rows[0]['exists'];

                if ($exists == 't') {
                    return new Response(true, "User is already registered");
                }

                return new Response(false, "User does not exist");
            }
        }

        //Runs db query to check if user login details match any user in the database
        public static function getUser(string $loginID, string $password) {

            $result = Database::query("SELECT * FROM users WHERE (username = $1 OR email = $1) AND password = $2", [$loginID, $password]);

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

        //checks if a post with the same ID already exists just in case to prevent duplication
        public static function checkItemExist(string $item_id, string $table, string $id) {

            $result = Database::query("SELECT EXISTS (SELECT 1 FROM $table WHERE $id = $1)", [$item_id]);

            if ($result) {
                $rows = pg_fetch_all($result);
                return $rows[0]['exists'] === 't';
            }

            return false;
        }

        public static function checkUsernameOrEmailTaken(string $username, string $email, string $excludeUserID) {
            $usernameTaken = Database::query(
                "SELECT 1 FROM users WHERE username = $1 AND user_id != $2",
                [$username, $excludeUserID]
            );

            if ($usernameTaken && pg_num_rows($usernameTaken) > 0) {
                return new Response(false, "Username is already taken");
            }

            $emailTaken = Database::query(
                "SELECT 1 FROM users WHERE email = $1 AND user_id != $2",
                [$email, $excludeUserID]
            );

            if ($emailTaken && pg_num_rows($emailTaken) > 0) {
                return new Response(false, "Email is already in use");
            }

            return new Response(true, "Available");
        }         
    }