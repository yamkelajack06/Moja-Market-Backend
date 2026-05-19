<?php
    require_once __DIR__ . '/../response/response.php';

    class Utility {
        public static function checkUserExists(string $username, string $email) {
            $result = Database::query(
                "SELECT EXISTS (SELECT 1 FROM users WHERE username = $1 OR email = $2)",
                [$username, $email]
            );

            if ($result != "") {
                $rows   = pg_fetch_all($result);
                $exists = $rows[0]['exists'];

                if ($exists == 't') {
                    return new Response(true, "User is already registered");
                }

                return new Response(false, "User does not exist");
            }
        }

        public static function getUser(string $loginID, string $password) {
            // Fetch by username or email only
            $result = Database::query(
                "SELECT * FROM users WHERE username = $1 OR email = $1",
                [$loginID]
            );

            if (!$result) {
                throw new ErrorException("Database query failed");
            }

            $rows = pg_fetch_all($result);

            if (empty($rows)) {
                return null;
            }

            $user = $rows[0];

            // Verify the plain-text input against the stored bcrypt hash
            if (!password_verify($password, $user['password'])) {
                return null;
            }

            // Strip the password hash before sending the user object to the client
            unset($user['password']);

            return json_encode($user);
        }

        public static function checkItemExist(string $item_id, string $table, string $id) {
            $result = Database::query(
                "SELECT EXISTS (SELECT 1 FROM $table WHERE $id = $1)",
                [$item_id]
            );

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