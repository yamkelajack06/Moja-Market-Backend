<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../response/response.php';
require_once __DIR__ . '/../../utils/utils.php';

class Rating {

    // User submits a rating for an item
    public static function submitRating($json) {
        try {
            $data        = json_decode($json, true);
            $ratingID    = $data['ratingID'];
            $itemID      = $data['itemID'];
            $raterID     = $data['raterID'];
            $ratingValue = intval($data['ratingValue']);

            // Validate rating range just in case
            if ($ratingValue < 1 || $ratingValue > 5) {
                return new Response(false, "Rating must be between 1 and 5");
            }

            // Check item exists
            $itemExists = Utility::checkItemExist($itemID, "item", "item_id");
            if (!$itemExists) {
                return new Response(false, "Item not found");
            }

            // Check user exists
            $userExists = Utility::checkItemExist($raterID, "users", "user_id");
            if (!$userExists) {
                return new Response(false, "User not found");
            }

            // Check if user already rated this item
            $alreadyRated = Database::query(
                "SELECT 1 FROM Rating WHERE item_id = $1 AND rater = $2",
                [$itemID, $raterID]
            );

            if ($alreadyRated && pg_num_rows($alreadyRated) > 0) {
                return new Response(false, "You have already rated this item");
            }

            $result = Database::query(
                "INSERT INTO Rating (rating_id, item_id, rating_value, rater) VALUES ($1, $2, $3, $4)",
                [$ratingID, $itemID, $ratingValue, $raterID]
            );

            if (!$result) {
                return new Response(false, "Failed to submit rating");
            }

            return new Response(true, "Rating submitted successfully");

        } catch (Exception $e) {
            return new Response(false, "An error occurred: " . $e->getMessage());
        }
    }

    // Returns the average rating for an item
    public static function getAverageRating($itemID) {
        try {
            $itemExists = Utility::checkItemExist($itemID, "item", "item_id");
            if (!$itemExists) {
                return new Response(false, "Item not found");
            }

            $result = Database::query(
                "SELECT ROUND(AVG(rating_value)::numeric, 1) AS average FROM Rating WHERE item_id = $1",
                [$itemID]
            );

            if (!$result) {
                return new Response(false, "Failed to get average rating");
            }

            $row     = pg_fetch_assoc($result);
            $average = $row['average'] ?? 0;

            return new Response(true, "Average rating retrieved", ['average' => (float) $average]);

        } catch (Exception $e) {
            return new Response(false, "An error occurred: " . $e->getMessage());
        }
    }

    // Returns the number of users who rated an item
    public static function getNumberOfRaters($itemID) {
        try {
            $itemExists = Utility::checkItemExist($itemID, "item", "item_id");
            if (!$itemExists) {
                return new Response(false, "Item not found");
            }

            $result = Database::query(
                "SELECT COUNT(*) AS total FROM Rating WHERE item_id = $1",
                [$itemID]
            );

            if (!$result) {
                return new Response(false, "Failed to get rater count");
            }

            $row   = pg_fetch_assoc($result);
            $total = $row['total'] ?? 0;

            return new Response(true, "Rater count retrieved", ['total' => (int) $total]);

        } catch (Exception $e) {
            return new Response(false, "An error occurred: " . $e->getMessage());
        }
    }
}