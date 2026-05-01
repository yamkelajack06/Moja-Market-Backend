<?php
    require_once __DIR__ . '/../../config/db.php';

    class ItemDatabase {
        
        //This will receive json from the front end and handle posting the item
        public static function postItem($json) {
            //convert the json into an associative array
            $item = json_decode($json,true);

            //get the variables from the json
            $itemID = $item['itemID'];
            $seller = $item['seller'];
            $userID = $seller['userID'];
            $datePosted = $item['datePosted'];
            $itemName = $item['itemName'];
            $itemDescription = $item['itemDescription'];
            $condition = $item['condition'];
            $sellerLocation = $item['sellerLocation'];
            $stockStatus = $item['stockStatus'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $averageRating = $item['averageRating'];
            $itemImage = $item['itemImage'];
            $imageID = $itemImage['imageID'];
            $imagePath = $itemImage['imagePath'];

            //for response
            $is_success = false;
            $item_exist = Utility::checkItemExist($itemID,"item","item_id");

            if ($item_exist) {
                return new Response(false,"Item already exists");
            }

            //just insert the post details into the database
            $query = "INSERT INTO item (item_id, user_id, date_posted, item_name, item_description, item_condition, location, status, stock_amount, item_price) VALUES (" .
                    "'" . $itemID . "'," .
                    "'" . $userID . "'," .
                    "'" . $datePosted . "'," .
                    "'" . $itemName . "'," .
                    "'" . $itemDescription . "'," .
                    "'" . $condition . "'," .
                    "'" . $sellerLocation . "'," .
                    "'" . $stockStatus . "'," .
                    $quantity . "," .
                    $price .
                    ")";
            
            $imageQuery = "INSERT INTO Image (image_id, image_data, item_id) VALUES (" .
            "'" . $imageID . "'," .
            "'" . $imagePath . "'," .
            "'" . $itemID . "'" .
            ")";

            try {
                //run the two database queries at once
                $result = Database::queryDatabase($query);
                $imageResult = Database::queryDatabase($imageQuery);

                //if one if them or both fail then the post fails
                if (!($result && $imageResult)) {
                    return new Response($is_success, "Failed to post item");
                    //TO DO Later: need some cleanup logic just in case on of them gets inserted into the database

                } else {
                    $is_success = true;
                }
            } catch (Exception $e) {
                return new Response($is_success, "An error occurred: ".$e -> getMessage());  
            }

            return new Response($is_success, "Item posted successfully");
        }

        public static function updateItem($json) {
            //convert the json into an associative array
            $item = json_decode($json,true);

            //Assign properties to a real name
            $itemID = $item['itemID'];
            $seller = $item['seller']; //because seller is an object
            $userID = $seller['userID'];
            $datePosted = $item['datePosted'];
            $itemName = $item['itemName'];
            $itemDescription = $item['itemDescription'];
            $condition = $item['condition'];
            $sellerLocation = $item['sellerLocation'];
            $stockStatus = $item['stockStatus'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $averageRating = $item['averageRating'];
            $itemImage = $item['itemImage'];

            //for response
            $is_success = false;

          $query = "UPDATE item SET
                    user_id = '" . $userID . "',
                    date_posted = '" . $datePosted . "',
                    item_name = '" . $itemName . "',
                    item_description = '" . $itemDescription . "',
                    item_condition = '" . $condition . "',
                    location = '" . $sellerLocation . "',
                    status = '" . $stockStatus . "',
                    stock_amount = " . $quantity . ",
                    item_price = " . $price . "
                    WHERE item_id = '" . $itemID . "'";

            $item_exist = Utility::checkItemExist($itemID,"item","item_id");

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

        public static function deleteItem($itemID) {

            $item_exist = Utility::checkItemExist($itemID,"item","item_id");

            if (!$item_exist) {
                return new Response(false, "Item to delete not found");
            }

            try {
                $is_success = false;

                $query = "DELETE FROM item WHERE item_id = '" . $itemID . "'";
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

        public static function getItemDetails($itemID) {
            try {
                $is_success = false;

                $query = "SELECT * FROM item WHERE item_id = '" . $itemID . "'";
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return null; //item not found
                } else {
                    $rows = pg_fetch_all($result);
                    $item_details = json_encode($rows);
                    return $item_details;
                }
            } catch (Exception $e) {
                return null;
            }
        }

        public function getFeed() {
            try {
                $query = "SELECT * FROM item";
                $result = Database::queryDatabase($query);

                if ($result) {
                    $rows = pg_fetch_all($rows);
                    return json_encode($rows);
                } else {
                    return json_encode([]);
                }

            } catch (Exception $e) {
                return new Exception($e -> getMessage());
            }
        }
    }