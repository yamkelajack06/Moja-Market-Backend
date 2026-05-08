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
            try {
                $result = Database::queryDatabase($query);
        
                if (!$result) {
                    return new Response(false, "Failed to post item");

                } else {
                    //Insert the image in the database after inserting the item is successful
                    $imageResult = ItemDatabase::postImage($imageID, $imagePath,$itemID);

                    //check if inserting the image is successful, if not just delete the item from the database
                    if (!$imageResult -> getSuccess()) {
                        ItemDatabase::deleteItem($itemID);
                        return new Response(false, "Failed to post image");
                    }
                }
            } catch (Exception $e) {
                
                return new Response(false, "An error occurred: ".$e -> getMessage());  
            }

            return new Response(true, "Item posted successfully");
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
                    return new Response(true, "Item updated successfully");
                }

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }
        }

        public static function deleteItem($json) {
            $item = json_decode($json,true);
            $itemID = $item['itemID'];
            $itemImage = $item['itemImage'];
            $imageID = $itemImage['imageID'];

            $item_exist = Utility::checkItemExist($itemID,"item","item_id");

            if (!$item_exist) {
                return new Response(false, "Item to delete not found");
            }

            try {
                $query = "DELETE FROM item WHERE item_id = '" . $itemID . "'";
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return new Response(false, "Failed to post item");
                } else {
                    ItemDatabase::deleteImage($imageID);
                }
            } catch (Exception $e) {
                return new Response(false, "An error occurred: ".$e -> getMessage());
            }

            return new Response(true, "Item deleted successfully");
        }

         public static function getItemDetails($itemID) {
    try {
        $query = "SELECT item.*, Image.image_id, Image.image_data, users.name, users.surname, users.username 
                  FROM item 
                  LEFT JOIN Image ON item.item_id = Image.item_id 
                  LEFT JOIN users ON item.user_id = users.user_id
                  WHERE item.item_id = '" . $itemID . "'";
                  
        $result = Database::queryDatabase($query);
        if (!$result) {
            return new Response(false, "Item not found");
        } else {
            $rows = pg_fetch_all($result);
            return new Response(true, "Item found", $rows ? $rows : []);
        }
    } catch (Exception $e) {
        return new Response(false, $e->getMessage());
    }
}

        public static function getFeed() {
            try {
                $query = "SELECT item.*, Image.image_id, Image.image_data, users.name, users.surname, users.username 
                          FROM item 
                          LEFT JOIN Image ON item.item_id = Image.item_id
                          LEFT JOIN users ON item.user_id = users.user_id";
                          
                $result = Database::queryDatabase($query);
                if ($result) {
                    $rows = pg_fetch_all($result) ?: [];
                    return new Response(true, "Feed loaded", $rows);
                } else {
                    return new Response(false, "Failed to load feed", []);
                }
            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }
        }

        public static function postImage($imageID, $imagePath, $itemID) {
            try {
                $query = "INSERT INTO Image (image_id, image_data, item_id) VALUES (" .
                "'" . $imageID . "'," .
                "'" . $imagePath . "'," .
                "'" . $itemID . "'" .
                ")";

                $result = Database::queryDatabase($query);

                if (!$result) {
                    return new Response(false, "Failed to post image");
                }

                return new Response(true, "Image posted successfully");

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }
        }

        public static function deleteImage($imageID) {
            try {
                $image_exist = Utility::checkItemExist($imageID, "Image", "image_id");

            if (!$image_exist) {
                return new Response(false, "Image to delete not found");
            }

            $query = "DELETE FROM Image WHERE image_id = '" . $imageID . "'";
            $result = Database::queryDatabase($query);

            if (!$result) {
                return new Response(false, "Failed to delete image");
            }

            return new Response(true, "Image deleted successfully");

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }
        }
    }

