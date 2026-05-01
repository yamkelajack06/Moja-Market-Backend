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
                } else {
                    $is_success = true;
                }
            } catch (Exception $e) {
                return new Response($is_success, "An error occurred: ".$e -> getMessage());  
            }

            return new Response($is_success, "Item posted successfully");
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

            //for response
            $is_success = false;

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
                    $is_success = true;
                    return new Response($is_success, "Item updated successfully");
                }

            } catch (Exception $e) {
                return new Response($is_success, "An error occurred: " . $e->getMessage());
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
                } else {
                    $is_success = true;
                }
            } catch (Exception $e) {
                return new Response($is_success, "An error occurred: ".$e -> getMessage());
            }

            return new Response($is_success, "Item deleted successfully");
        }

        public static function getWantRequestDetails($wantsID) {
            try {
                $is_success = false;

                $query = "SELECT * FROM wantrequest WHERE wants_id = '" . $wantsID . "'";
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return null; //item not found
                } else {
                    $rows = pg_fetch_all($result);
                    $want_request_details = json_encode($rows);
                    return $want_request_details;
                }
            } catch (Exception $e) {
                return null;
            }
        }

        public function getWantRequestFeed() {
            try {
                $query = "SELECT * FROM wantrequest";
                $result = Database::queryDatabase($query);

                if ($result) {
                    $rows = pg_fetch_all($result);
                    return json_encode($rows);
                } else {
                    return json_encode([]);
                }

            } catch (Exception $e) {
                return new Exception($e -> getMessage());
            }
        }
    }
