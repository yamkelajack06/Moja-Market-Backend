<?php
    require_once __DIR__ . '/../../config/db.php';

    class ItemDatabase {
        
        //This will receive json from the front end and handle posting the item
        public static function postItem($json) {
            //convert the json into an associative array
            $item = json_encode($json,true);

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

            //for response
            $is_success = false;

            //just insert the post details into the database
            $query = "INSERT INTO items VALUES(" .
                        $itemID . "," .
                        $userID . "," .
                        "'" . $datePosted . "'," .
                        "'" . $itemName . "'," .
                        "'" . $itemDescription . "'," .
                        "'" . $condition . "'," .
                        "'" . $sellerLocation . "'," .
                        "'" . $stockStatus . "'," .
                        $quantity . "," .
                        $price . "," .
                        $averageRating . "," .
                        "'" . $itemImage . "'" .
                    ")";

            try {
                $result = Database::queryDatabase($query);

                if (!result) {
                    return new Response($is_success, "Failed to post item");
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
            $item = json_encode($json,true);

            //Assign properties to a real name
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

            //for response
            $is_success = false;

            $query = "UPDATE items SET 
                        sellerID = " . $userID . ",
                        datePosted = '" . $datePosted . "',
                        itemName = '" . $itemName . "',
                        itemDescription = '" . $itemDescription . "',
                        `condition` = '" . $condition . "',
                        sellerLocation = '" . $sellerLocation . "',
                        stockStatus = '" . $stockStatus . "',
                        quantity = " . $quantity . ",
                        price = " . $price . ",
                        averageRating = " . $averageRating . ",
                            itemImage = '" . $itemImage . "'
                        WHERE itemID = " . $itemID;

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
            try {
                $is_success = false;

                $query = "DELETE * FROM items WHERE item_id =" .$itemID;
                Database::queryDatabase($query);

                if (!result) {
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

                $query = "SELECT * FROM items WHERE item_id = ".$itemID;
                $result = Database::queryDatabase($query);

                if (!result) {
                    return null; //item not found
                } else {
                    $rows = pg_fetch_all($result);
                    $item_details = json_encode($rows);
                }
            } catch (Exception $e) {
                return null;
            }
        }
    }