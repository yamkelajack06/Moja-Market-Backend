<?php
    require_once __DIR__ . '/../../config/db.php';

    class ItemDatabase {

        public static function postItem($json) {
            $item = json_decode($json, true);

            $itemID          = $item['itemID'];
            $seller          = $item['seller'];
            $userID          = $seller['userID'];
            $datePosted      = $item['datePosted'];
            $itemName        = $item['itemName'];
            $itemDescription = $item['itemDescription'];
            $condition       = $item['condition'];
            $sellerLocation  = $item['sellerLocation'];
            $stockStatus     = $item['stockStatus'];
            $quantity        = $item['quantity'];
            $price           = $item['price'];

            // Extra images sent as an array of URLs
            $extraImages = isset($item['images']) && is_array($item['images'])
                ? $item['images']
                : [];

            $item_exist = Utility::checkItemExist($itemID, "item", "item_id");

            if ($item_exist) {
                return new Response(false, "Item already exists");
            }

            try {
                $result = Database::query("INSERT INTO item (item_id, user_id, date_posted, item_name, item_description, item_condition, location, status, stock_amount, item_price) VALUES ($1, $2, $3, $4, $5, $6, $7, $8, $9, $10)", [$itemID, $userID, $datePosted, $itemName, $itemDescription, $condition, $sellerLocation, $stockStatus, $quantity, $price]);

                if (!$result) {
                    return new Response(false, "Failed to post item");
                }

                foreach ($extraImages as $url) {
                    if (empty($url)) continue;
                    $imgResult = ItemDatabase::postImage(
                        uniqid('img_'),
                        pg_escape_string($url),
                        $itemID
                    );

                    if (!$imgResult->getSuccess()) {
                        ItemDatabase::deleteItem(json_encode(['itemID' => $itemID]));
                        return new Response(false, "Failed to store image: " . $url);
                    }
                }

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }

            return new Response(true, "Item posted successfully");
        }

        public static function updateItem($json) {
            $item = json_decode($json, true);

            $itemID          = $item['itemID'];
            $seller          = $item['seller'];
            $userID          = $seller['userID'];
            $datePosted      = $item['datePosted'];
            $itemName        = $item['itemName'];
            $itemDescription = $item['itemDescription'];
            $condition       = $item['condition'];
            $sellerLocation  = $item['sellerLocation'];
            $stockStatus     = $item['stockStatus'];
            $quantity        = $item['quantity'];
            $price           = $item['price'];

            // Updated image URLs sent from the Android app
            $images = isset($item['images']) && is_array($item['images'])
                ? $item['images']
                : [];

            $item_exist = Utility::checkItemExist($itemID, "item", "item_id");

            if (!$item_exist) {
                return new Response(false, "Item to update not found");
            }

            try {
                $result = Database::query("UPDATE item SET user_id = $1, date_posted = $2, item_name = $3, item_description = $4, item_condition = $5, location = $6, status = $7, stock_amount = $8, item_price = $9 WHERE item_id = $10", [$userID, $datePosted, $itemName, $itemDescription, $condition, $sellerLocation, $stockStatus, $quantity, $price, $itemID]);

                if (!$result) {
                    return new Response(false, "Failed to update item");
                }

                // Replace all image rows for this item with the updated list
                Database::query("DELETE FROM Image WHERE item_id = '" . $itemID . "'");

                foreach ($images as $url) {
                    if (empty($url)) continue;
                    $imgResult = ItemDatabase::postImage(
                        uniqid('img_'),
                        pg_escape_string($url),
                        $itemID
                    );

                    if (!$imgResult->getSuccess()) {
                        return new Response(false, "Item updated but failed to store image: " . $url);
                    }
                }

                return new Response(true, "Item updated successfully");

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }
        }

        public static function deleteItem($json) {
            $item   = json_decode($json, true);
            $itemID = $item['itemID'];

            $item_exist = Utility::checkItemExist($itemID, "item", "item_id");

            if (!$item_exist) {
                return new Response(false, "Item to delete not found");
            }

            try {
                Database::query("DELETE FROM Image WHERE item_id = '" . $itemID . "'");

                $result = Database::query("DELETE FROM item WHERE item_id = $1", [$itemID]);

                if (!$result) {
                    return new Response(false, "Failed to delete item");
                }

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }

            return new Response(true, "Item deleted successfully");
        }

        public static function getFeed() {
            try {
                $result = Database::query("SELECT item.*, Image.image_id, Image.image_data, users.name, users.surname, users.username, users.email FROM item LEFT JOIN Image ON item.item_id = Image.item_id LEFT JOIN users ON item.user_id = users.user_id ORDER BY item.date_posted DESC", []);

                if (!$result) {
                    return new Response(false, "Failed to load feed", []);
                }

                $rows = pg_fetch_all($result) ?: [];

                $grouped = [];
                foreach ($rows as $row) {
                    $id = $row['item_id'];
                    if (!isset($grouped[$id])) {
                        $grouped[$id] = $row;
                        $grouped[$id]['images'] = [];
                    }
                    if (!empty($row['image_data'])) {
                        $grouped[$id]['images'][] = $row['image_data'];
                    }
                }

                return new Response(true, "Feed loaded", array_values($grouped));

            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }
        }

        public static function postImage($imageID, $imagePath, $itemID) {
            try {
               
                $result = Database::query("INSERT INTO Image (image_id, image_data, item_id) VALUES ($1, $2, $3)", [$imageID, $imagePath, $itemID]);

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

                $result = Database::query("DELETE FROM Image WHERE image_id = $1", [$imageID]);

                if (!$result) {
                    return new Response(false, "Failed to delete image");
                }

                return new Response(true, "Image deleted successfully");

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }
        }

        // Collapses multiple JOIN rows into a single item array
        private static function collapseImageRows(array $rows): array {
            $item = $rows[0];
            $item['images'] = [];
            foreach ($rows as $row) {
                if (!empty($row['image_data'])) {
                    $item['images'][] = $row['image_data'];
                }
            }
            $item['image_data'] = !empty($item['images']) ? $item['images'][0] : '';
            return $item;
        }
    }