<?php
    class WantsDatabase {
             //This will receive json from the front end and handle posting the item
        public static function postWantRequest($json) {
            //convert the json into an associative array
            $item = json_decode($json,true);

            //get the variables from the json
            $wantsID = $item['id'];
            $buyer = $item['buyer'];
            $userID = $buyer['userID'];
            $datePosted = $item['datePosted'];
            $itemName = $item['item'];
            $description = $item['description'];
            $budget = $item['budget'];
            $wantStatus = $item['wantStatus'];

            //for response
            $is_success = false;
            $item_exist = Utility::checkItemExist($wantsID,"wantrequest","wants_id");

            if ($item_exist) {
                return new Response(false,"Item already exists");
            }

            //just insert the want request details into the database
            $query = "INSERT INTO wantrequest (wants_id, user_id, date_posted, item_name, item_description, budget, status) VALUES (" .
                        "'" . $wantsID . "'," .
                        "'" . $userID . "'," .
                        "'" . $datePosted . "'," .
                        "'" . $itemName . "'," .
                        "'" . $description . "'," .
                        $budget . "," .
                        "'" . $wantStatus . "'" .
                        ")";

            try {
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return new Response($is_success, "Failed to post item");
                }

            } catch (Exception $e) {
                return new Response(false, "An error occurred: ".$e -> getMessage());  
            }

            return new Response(true, "Item posted successfully");
        }

        public static function updateWantRequest($json) {
            //convert the json into an associative array
            $item = json_decode($json,true);

            //Assign properties to a real name
            $wantsID = $item['id'];
            $buyer = $item['buyer'];
            $userID = $buyer['userID'];
            $datePosted = $item['datePosted'];
            $itemName = $item['item'];
            $description = $item['description'];
            $budget = $item['budget'];
            $wantStatus = $item['wantStatus'];

          $query = "UPDATE wantrequest SET
                user_id = '" . $userID . "',
                date_posted = '" . $datePosted . "',
                item_name = '" . $itemName . "',
                item_description = '" . $description . "',
                budget = " . $budget . ",
                status = '" . $wantStatus . "'
                WHERE wants_id = '" . $wantsID . "'";

            $item_exist = Utility::checkItemExist($wantsID,"wantrequest","wants_id");

            if (!$item_exist) {
                return new Response(false, "Item to update not found");
            }

            try {
                $result = Database::queryDatabase($query);

                if ($result) {
                    return new Response(true, "Item updated successfully");
                }

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }
        }

        public static function deleteWantRequest($wantsID) {

            $item_exist = Utility::checkItemExist($wantsID,"wantrequest","wants_id");

            if (!$item_exist) {
                return new Response(false, "Item to delete not found");
            }

            try {
                $is_success = false;

                $query = "DELETE FROM wantrequest WHERE wants_id = '" . $wantsID . "'";
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return new Response($is_success, "Failed to post item");
                }

            } catch (Exception $e) {
                return new Response(false, "An error occurred: ".$e -> getMessage());
            }

            return new Response(true, "Item deleted successfully");
        }

         public static function getWantRequestDetails($wantsID) {
            try {
                $query = "SELECT wantrequest.*, users.name, users.surname, users.username 
                          FROM wantrequest 
                          LEFT JOIN users ON wantrequest.user_id = users.user_id
                          WHERE wantrequest.wants_id = '" . $wantsID . "'";
                          
                $result = Database::queryDatabase($query);
                if (!$result) {
                    return new Response(false, "want request not found");
                } else {
                    $rows = pg_fetch_all($result);
                    return new Response(true, "Want request found", $rows);
                }
            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }
        }

        public function getWantRequestFeed() {
            try {
                $query = "SELECT wantrequest.*, users.name, users.surname, users.username, users.email
                          FROM wantrequest
                          LEFT JOIN users ON wantrequest.user_id = users.user_id";
                          
                $result = Database::queryDatabase($query);
                if ($result) {
                    $rows = pg_fetch_all($result) ?: [];
                    return new Response(true, "Want request feed loaded", $rows);
                } else {
                    return new Response(false, "Failed to load want request feed");
                }
            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }
        }
    }
