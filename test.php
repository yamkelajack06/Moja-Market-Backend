<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/response/response.php';
require_once __DIR__ . '/utils/utils.php';
require_once __DIR__ . '/api/auth/register.php';
require_once __DIR__ . '/api/auth/login.php';
require_once __DIR__ . '/api/posts/items.php';
require_once __DIR__ . '/api/posts/wants.php';
require_once __DIR__ . '/api/user/profile.php';
require_once __DIR__ . '/api/user/listings.php';
require_once __DIR__ . '/api/user/want_requests.php';
require_once __DIR__ . '/api/rating/rating.php';

$passed = 0;
$failed = 0;

function test(string $name, bool $condition, mixed $data = null) {
    global $passed, $failed;
    if ($condition) {
        echo "\e[32m[PASS]\e[0m $name\n";
        $passed++;
    } else {
        echo "\e[31m[FAIL]\e[0m $name\n";
        if ($data !== null) {
            echo "       Details: " . print_r($data, true) . "\n";
        }
        $failed++;
    }
}

echo "\n========================================\n";
echo "         MOJA MARKET TEST SUITE         \n";
echo "========================================\n\n";

// ── TEST DATA ──────────────────────────────
$userID   = 'test-user-' . uniqid();
$itemID   = 'test-item-' . uniqid();
$wantID   = 'test-want-' . uniqid();
$ratingID = 'test-rating-' . uniqid();
$username = 'testuser_' . uniqid();
$email    = 'test_' . uniqid() . '@example.com';

// ── DATABASE CONNECTION ────────────────────
echo "--- Database ---\n";
try {
    Database::connect();
    test("Database connects successfully", true);
} catch (Exception $e) {
    test("Database connects successfully", false, $e->getMessage());
    echo "\nCannot continue without database connection.\n";
    exit(1);
}

// ── REGISTER ──────────────────────────────
echo "\n--- Register ---\n";
$registerJson = json_encode([
    'userID'   => $userID,
    'name'     => 'Test',
    'surname'  => 'User',
    'username' => $username,
    'email'    => $email,
    'password' => 'password123'
]);

$registerResult = Register::registerUser($registerJson);
test("Register new user", $registerResult->getSuccess(), $registerResult->getMessage());

// Register duplicate
$registerDupe = Register::registerUser($registerJson);
test("Block duplicate registration", !$registerDupe->getSuccess(), $registerDupe->getMessage());

// ── LOGIN ──────────────────────────────────
echo "\n--- Login ---\n";
$loginResult = Login::login($username, 'password123');
test("Login with username", $loginResult->getSuccess(), $loginResult->getMessage());

$loginEmail = Login::login($email, 'password123');
test("Login with email", $loginEmail->getSuccess(), $loginEmail->getMessage());

$loginWrong = Login::login($username, 'wrongpassword');
test("Block wrong password", !$loginWrong->getSuccess(), $loginWrong->getMessage());

// ── USER PROFILE ───────────────────────────
echo "\n--- User Profile ---\n";
$profileJson = json_encode(['userID' => $userID]);
$profileResult = UserProfile::getUserProfile($profileJson);
test("Get user profile", $profileResult->getSuccess(), $profileResult->getMessage());

$updateProfileJson = json_encode([
    'userID'   => $userID,
    'name'     => 'Updated',
    'surname'  => 'User',
    'username' => $username,
    'email'    => $email,
    'password' => 'password123'
]);
$updateProfileResult = UserProfile::updateProfile($updateProfileJson);
test("Update user profile", $updateProfileResult->getSuccess(), $updateProfileResult->getMessage());

// ── POST ITEM ──────────────────────────────
echo "\n--- Items ---\n";
$itemJson = json_encode([
    'itemID'          => $itemID,
    'seller'          => ['userID' => $userID],
    'datePosted'      => date('Y-m-d H:i:s'),
    'itemName'        => 'Test Item',
    'itemDescription' => 'A test item description',
    'condition'       => 'New',
    'sellerLocation'  => 'Johannesburg',
    'stockStatus'     => 'Available',
    'quantity'        => 5,
    'price'           => 99.99,
    'images'          => []
]);

$postItemResult = ItemDatabase::postItem($itemJson);
test("Post new item", $postItemResult->getSuccess(), $postItemResult->getMessage());

// Post duplicate item
$postDupe = ItemDatabase::postItem($itemJson);
test("Block duplicate item", !$postDupe->getSuccess(), $postDupe->getMessage());

// Get feed
$feedResult = ItemDatabase::getFeed();
test("Get items feed", $feedResult->getSuccess(), $feedResult->getMessage());

// Update item
$updateItemJson = json_encode([
    'itemID'          => $itemID,
    'seller'          => ['userID' => $userID],
    'datePosted'      => date('Y-m-d H:i:s'),
    'itemName'        => 'Updated Test Item',
    'itemDescription' => 'Updated description',
    'condition'       => 'Used',
    'sellerLocation'  => 'Cape Town',
    'stockStatus'     => 'Available',
    'quantity'        => 3,
    'price'           => 79.99,
    'images'          => []
]);
$updateItemResult = ItemDatabase::updateItem($updateItemJson);
test("Update item", $updateItemResult->getSuccess(), $updateItemResult->getMessage());

// Get user listings
$listingsResult = UserListings::getUserListings($userID);
test("Get user listings", $listingsResult->getSuccess(), $listingsResult->getMessage());

// ── WANT REQUESTS ──────────────────────────
echo "\n--- Want Requests ---\n";
$wantJson = json_encode([
    'id'          => $wantID,
    'buyer'       => ['userID' => $userID],
    'datePosted'  => date('Y-m-d H:i:s'),
    'item'        => 'Test Want Item',
    'description' => 'Looking for this item',
    'budget'      => 500.00,
    'wantStatus'  => 'true'
]);

$postWantResult = WantsDatabase::postWantRequest($wantJson);
test("Post want request", $postWantResult->getSuccess(), $postWantResult->getMessage());

// Get want feed
$wantFeedResult = (new WantsDatabase())->getWantRequestFeed();
test("Get wants feed", $wantFeedResult->getSuccess(), $wantFeedResult->getMessage());

// Update want
$updateWantJson = json_encode([
    'id'          => $wantID,
    'buyer'       => ['userID' => $userID],
    'datePosted'  => date('Y-m-d H:i:s'),
    'item'        => 'Updated Want Item',
    'description' => 'Updated description',
    'budget'      => 300.00,
    'wantStatus'  => 'true'
]);
$updateWantResult = WantsDatabase::updateWantRequest($updateWantJson);
test("Update want request", $updateWantResult->getSuccess(), $updateWantResult->getMessage());

// Get user want requests
$userWantsResult = WantRequest::getUserWantRequests($userID);
test("Get user want requests", $userWantsResult->getSuccess(), $userWantsResult->getMessage());

// ── RATINGS ────────────────────────────────
echo "\n--- Ratings ---\n";
$ratingJson = json_encode([
    'ratingID'    => $ratingID,
    'itemID'      => $itemID,
    'raterID'     => $userID,
    'ratingValue' => 4
]);

$submitRatingResult = Rating::submitRating($ratingJson);
test("Submit rating", $submitRatingResult->getSuccess(), $submitRatingResult->getMessage());

// Submit duplicate rating
$submitDupeRating = Rating::submitRating($ratingJson);
test("Block duplicate rating", !$submitDupeRating->getSuccess(), $submitDupeRating->getMessage());

// Get average rating
$averageResult = Rating::getAverageRating($itemID);
test("Get average rating", $averageResult->getSuccess(), $averageResult->getMessage());

// Get rater count
$countResult = Rating::getNumberOfRaters($itemID);
test("Get rater count", $countResult->getSuccess(), $countResult->getMessage());

// ── CLEANUP ────────────────────────────────
echo "\n--- Cleanup ---\n";

$deleteWantResult = WantsDatabase::deleteWantRequest($wantID);
test("Delete want request", $deleteWantResult->getSuccess(), $deleteWantResult->getMessage());

$deleteItemResult = ItemDatabase::deleteItem(json_encode(['itemID' => $itemID]));
test("Delete item", $deleteItemResult->getSuccess(), $deleteItemResult->getMessage());

// ── SUMMARY ────────────────────────────────
echo "\n========================================\n";
echo " PASSED: $passed\n";
echo " FAILED: $failed\n";
echo " TOTAL:  " . ($passed + $failed) . "\n";
echo "========================================\n\n";