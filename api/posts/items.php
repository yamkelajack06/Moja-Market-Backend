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
            $itemImage       = $item['itemImage'];
            $imageID         = $itemImage['imageID'];
            $imagePath       = $itemImage['imagePath'];

            // Extra images sent as an array of URLs alongside the primary
            $extraImages = isset($item['images']) && is_array($item['images'])
                ? $item['images']
                : [];

            $item_exist = Utility::checkItemExist($itemID, "item", "item_id");

            if ($item_exist) {
                return new Response(false, "Item already exists");
            }

            $query = "INSERT INTO item
                        (item_id, user_id, date_posted, item_name, item_description,
                         item_condition, location, status, stock_amount, item_price)
                      VALUES (
                        '" . $itemID . "',
                        '" . $userID . "',
                        '" . $datePosted . "',
                        '" . pg_escape_string($itemName) . "',
                        '" . pg_escape_string($itemDescription) . "',
                        '" . pg_escape_string($condition) . "',
                        '" . pg_escape_string($sellerLocation) . "',
                        '" . pg_escape_string($stockStatus) . "',
                        " . intval($quantity) . ",
                        " . floatval($price) . "
                      )";

            try {
                $result = Database::queryDatabase($query);

                if (!$result) {
                    return new Response(false, "Failed to post item");
                }

                // Build a de-duplicated list: primary image first, then extras
                $allUrls = [$imagePath];
                foreach ($extraImages as $url) {
                    if (!empty($url) && !in_array($url, $allUrls)) {
                        $allUrls[] = $url;
                    }
                }

                foreach ($allUrls as $url) {
                    $imgResult = ItemDatabase::postImage(
                        uniqid('img_'),
                        pg_escape_string($url),
                        $itemID
                    );

                    // Roll back the item if any image insert fails
                    if (!$imgResult->getSuccess()) {
                        ItemDatabase::deleteItem(json_encode([
                            'itemID'    => $itemID,
                            'itemImage' => ['imageID' => $imageID]
                        ]));
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

            $item_exist = Utility::checkItemExist($itemID, "item", "item_id");

            if (!$item_exist) {
                return new Response(false, "Item to update not found");
            }

            $query = "UPDATE item SET
                        user_id          = '" . $userID . "',
                        date_posted      = '" . $datePosted . "',
                        item_name        = '" . pg_escape_string($itemName) . "',
                        item_description = '" . pg_escape_string($itemDescription) . "',
                        item_condition   = '" . pg_escape_string($condition) . "',
                        location         = '" . pg_escape_string($sellerLocation) . "',
                        status           = '" . pg_escape_string($stockStatus) . "',
                        stock_amount     = " . intval($quantity) . ",
                        item_price       = " . floatval($price) . "
                      WHERE item_id = '" . $itemID . "'";

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
            $item   = json_decode($json, true);
            $itemID = $item['itemID'];

            $item_exist = Utility::checkItemExist($itemID, "item", "item_id");

            if (!$item_exist) {
                return new Response(false, "Item to delete not found");
            }

            try {
                // Remove all images for this item before deleting the item row
                Database::queryDatabase("DELETE FROM Image WHERE item_id = '" . $itemID . "'");

                $result = Database::queryDatabase("DELETE FROM item WHERE item_id = '" . $itemID . "'");

                if (!$result) {
                    return new Response(false, "Failed to delete item");
                }

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }

            return new Response(true, "Item deleted successfully");
        }

        public static function getItemDetails($itemID) {
            try {
                // Join images and seller info; one row per image for this item
                $query = "SELECT item.*, Image.image_id, Image.image_data,
                                 users.name, users.surname, users.username
                          FROM item
                          LEFT JOIN Image ON item.item_id = Image.item_id
                          LEFT JOIN users ON item.user_id = users.user_id
                          WHERE item.item_id = '" . $itemID . "'";

                $result = Database::queryDatabase($query);

                if (!$result) {
                    return new Response(false, "Item not found");
                }

                $rows = pg_fetch_all($result);

                if (!$rows) {
                    return new Response(false, "Item not found");
                }

                // Collapse multiple image rows into a single item with an images array
                $collapsed = self::collapseImageRows($rows);
                return new Response(true, "Item found", [$collapsed]);

            } catch (Exception $e) {
                return new Response(false, $e->getMessage());
            }
        }

        public static function getFeed() {
            try {
                $query = "SELECT item.*, Image.image_id, Image.image_data,
                                 users.name, users.surname, users.username
                          FROM item
                          LEFT JOIN Image ON item.item_id = Image.item_id
                          LEFT JOIN users ON item.user_id = users.user_id
                          ORDER BY item.date_posted DESC";

                $result = Database::queryDatabase($query);

                if (!$result) {
                    return new Response(false, "Failed to load feed", []);
                }

                $rows = pg_fetch_all($result) ?: [];

                // Group rows by item_id, collecting all image URLs per item
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
                $query = "INSERT INTO Image (image_id, image_data, item_id)
                          VALUES ('" . $imageID . "', '" . $imagePath . "', '" . $itemID . "')";

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

                $result = Database::queryDatabase("DELETE FROM Image WHERE image_id = '" . $imageID . "'");

                if (!$result) {
                    return new Response(false, "Failed to delete image");
                }

                return new Response(true, "Image deleted successfully");

            } catch (Exception $e) {
                return new Response(false, "An error occurred: " . $e->getMessage());
            }
        }

        // Collapses multiple JOIN rows (one per image) into a single item array
        private static function collapseImageRows(array $rows): array {
            $item = $rows[0];
            $item['images'] = [];
            foreach ($rows as $row) {
                if (!empty($row['image_data'])) {
                    $item['images'][] = $row['image_data'];
                }
            }
            // Keep image_data as the primary image for backward compatibility
            $item['image_data'] = !empty($item['images']) ? $item['images'][0] : '';
            return $item;
        }
    }