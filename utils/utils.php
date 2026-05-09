<?php
    require_once __DIR__ . '/../response/response.php';
    class Utility {
        //Runs DB query to check if user exists or not for registering
        public static function checkUserExists(string $username, string $email) {
            $is_found = false;
            $query = "SELECT EXISTS (SELECT 1 FROM users WHERE username = '" . $username . "' OR email = '" . $email . "')";
            $result = Database::queryDatabase($query);

            if ($result != "") {
                $rows = pg_fetch_all($result);
                $exists = $rows[0]['exists'];

                if ($exists == 't') {
                    $is_found = true;
                    return new Response($is_found, "User is already registered");
                }

                return new Response($is_found, "User does not exist");
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

        //checks if a post with the same ID already exists just in case to prevent duplication
        public static function checkItemExist(string $item_id, string $table, string $id) {
            $query = "SELECT EXISTS (SELECT 1 FROM " . $table . " WHERE " . $id . " = '" . $item_id . "')";
            $result = Database::queryDatabase($query);

            if ($result) {
                $rows = pg_fetch_all($result);
                return $rows[0]['exists'] === 't';
            }

            return false;
        }

          public static function getChatByItem(string $user1, string $user2, string $itemID) {
            $query = "SELECT chat_id FROM Chat
                      WHERE ((user_1 = '" . $user1 . "' AND user_2 = '" . $user2 . "')
                          OR (user_1 = '" . $user2 . "' AND user_2 = '" . $user1 . "'))
                      AND item_id = '" . $itemID . "'
                      LIMIT 1";
 
            $result = Database::queryDatabase($query);
 
            if ($result && pg_num_rows($result) > 0) {
                $row = pg_fetch_assoc($result);
                return $row['chat_id'];
            }
 
            return null;
        }
 
        //checks if a chat already exists between two users for the same want request
        public static function getChatByWant(string $user1, string $user2, string $wantID) {
            $query = "SELECT chat_id FROM Chat
                      WHERE ((user_1 = '" . $user1 . "' AND user_2 = '" . $user2 . "')
                          OR (user_1 = '" . $user2 . "' AND user_2 = '" . $user1 . "'))
                      AND want_id = '" . $wantID . "'
                      LIMIT 1";
 
            $result = Database::queryDatabase($query);
 
            if ($result && pg_num_rows($result) > 0) {
                $row = pg_fetch_assoc($result);
                return $row['chat_id'];
            }
 
            return null;
        }
    }