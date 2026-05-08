<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/response/response.php';
require_once __DIR__ . '/utils/utils.php';
require_once __DIR__ . '/api/auth/login.php';
require_once __DIR__ . '/api/auth/register.php';
require_once __DIR__ . '/api/posts/items.php';
require_once __DIR__ . '/api/posts/wants.php';
require_once __DIR__ . '/api/user/profile.php';
require_once __DIR__ . '/api/user/listings.php';
require_once __DIR__ . '/api/user/want_requests.php';

// Generate Unique User Variables
$u1_id = uniqid('user_'); $u1_email = $u1_id . '@test.com'; $u1_uname = 'u_' . uniqid();
$u2_id = uniqid('user_'); $u2_email = $u2_id . '@test.com'; $u2_uname = 'u_' . uniqid();
$u3_id = uniqid('user_'); $u3_email = $u3_id . '@test.com'; $u3_uname = 'u_' . uniqid();

$password = 'Test@1234';

echo "=== STARTING MOJA MARKET API TESTS ===\n\n";

// --- AUTH TESTS ---
$login1 = Login::login($u1_email, $password);
echo "LOGIN 1: " . $login1->getMessage() . PHP_EOL;

$login2 = Login::login($u2_email, $password);
echo "LOGIN 2: " . $login2->getMessage() . PHP_EOL;

$login3 = Login::login($u3_email, $password);
echo "LOGIN 3: " . $login3->getMessage() . PHP_EOL;


// --- ITEM TESTS ---
$item1_id = uniqid('item_');
$item2_id = uniqid('item_');
$item3_id = uniqid('item_');

$item1 = json_encode(['itemID' => $item1_id, 'seller' => ['userID' => $u1_id], 'datePosted' => '2026-04-30 20:00:00', 'itemName' => 'Samsung S21', 'itemDescription' => 'Good condition', 'condition' => 'Used', 'sellerLocation' => 'Johannesburg', 'stockStatus' => 'In Stock', 'quantity' => 1, 'price' => 7500, 'averageRating' => 0, 'itemImage' => ['imageID' => uniqid('img_'), 'imagePath' => 's21.jpg']]);
$post1 = ItemDatabase::postItem($item1);
echo "POST ITEM 1: " . $post1->getMessage() . PHP_EOL;

$item2 = json_encode(['itemID' => $item2_id, 'seller' => ['userID' => $u2_id], 'datePosted' => '2026-04-30 21:00:00', 'itemName' => 'Nike Air Max 90', 'itemDescription' => 'Size 10, worn twice', 'condition' => 'Like New', 'sellerLocation' => 'Pretoria', 'stockStatus' => 'In Stock', 'quantity' => 1, 'price' => 1800, 'averageRating' => 0, 'itemImage' => ['imageID' => uniqid('img_'), 'imagePath' => 'airmax.jpg']]);
$post2 = ItemDatabase::postItem($item2);
echo "POST ITEM 2: " . $post2->getMessage() . PHP_EOL;

$item3 = json_encode(['itemID' => $item3_id, 'seller' => ['userID' => $u3_id], 'datePosted' => '2026-04-30 22:00:00', 'itemName' => 'PS5 Controller', 'itemDescription' => 'Brand new sealed', 'condition' => 'New', 'sellerLocation' => 'Sandton', 'stockStatus' => 'In Stock', 'quantity' => 2, 'price' => 1200, 'averageRating' => 0, 'itemImage' => ['imageID' => uniqid('img_'), 'imagePath' => 'ps5controller.jpg']]);
$post3 = ItemDatabase::postItem($item3);
echo "POST ITEM 3: " . $post3->getMessage() . PHP_EOL;

$newItem1 = json_encode(['itemID' => $item1_id, 'seller' => ['userID' => $u1_id], 'datePosted' => '2026-04-30 20:00:00', 'itemName' => 'Samsung S21 Ultra', 'itemDescription' => 'Excellent condition', 'condition' => 'Used', 'sellerLocation' => 'Johannesburg', 'stockStatus' => 'In Stock', 'quantity' => 1, 'price' => 9500, 'averageRating' => 4.9, 'itemImage' => ['imageID' => uniqid('img_'), 'imagePath' => 's21ultra.jpg']]);
$updateItem1 = ItemDatabase::updateItem($newItem1);
echo "UPDATE ITEM 1: " . $updateItem1->getMessage() . PHP_EOL;

// Get Items (Using ->toArray() to fix the empty {} issue)
echo "GET ITEM 1: " . json_encode(ItemDatabase::getItemDetails($item1_id)->toArray()) . PHP_EOL;
echo "GET ITEM 2: " . json_encode(ItemDatabase::getItemDetails($item2_id)->toArray()) . PHP_EOL;
echo "GET ITEM 3: " . json_encode(ItemDatabase::getItemDetails($item3_id)->toArray()) . PHP_EOL;
echo "GET FEED: " . json_encode(ItemDatabase::getFeed()->toArray()) . PHP_EOL;


// --- WANT TESTS ---
$want1_id = uniqid('want_');
$want2_id = uniqid('want_');
$want3_id = uniqid('want_');

$want1 = json_encode(['id' => $want1_id, 'buyer' => ['userID' => $u1_id], 'datePosted' => '2026-04-30 20:00:00', 'item' => 'MacBook Pro', 'description' => 'M1 chip minimum', 'budget' => 15000, 'wantStatus' => true]);
$postWant1 = WantsDatabase::postWantRequest($want1);
echo "POST WANT 1: " . $postWant1->getMessage() . PHP_EOL;

$want2 = json_encode(['id' => $want2_id, 'buyer' => ['userID' => $u2_id], 'datePosted' => '2026-04-30 21:00:00', 'item' => 'iPhone 13', 'description' => 'Any colour, good condition', 'budget' => 8000, 'wantStatus' => true]);
$postWant2 = WantsDatabase::postWantRequest($want2);
echo "POST WANT 2: " . $postWant2->getMessage() . PHP_EOL;

$want3 = json_encode(['id' => $want3_id, 'buyer' => ['userID' => $u3_id], 'datePosted' => '2026-04-30 22:00:00', 'item' => 'Gaming Chair', 'description' => 'Preferably ergonomic', 'budget' => 3000, 'wantStatus' => true]);
$postWant3 = WantsDatabase::postWantRequest($want3);
echo "POST WANT 3: " . $postWant3->getMessage() . PHP_EOL;

$newWant1 = json_encode(['id' => $want1_id, 'buyer' => ['userID' => $u1_id], 'datePosted' => '2026-04-30 20:00:00', 'item' => 'MacBook Pro M2', 'description' => 'M2 chip specifically', 'budget' => 18000, 'wantStatus' => true]);
$updateWant1 = WantsDatabase::updateWantRequest($newWant1);
echo "UPDATE WANT 1: " . $updateWant1->getMessage() . PHP_EOL;

// Get Wants
echo "GET WANT 1: " . json_encode(WantsDatabase::getWantRequestDetails($want1_id)->toArray()) . PHP_EOL;
echo "GET WANT 2: " . json_encode(WantsDatabase::getWantRequestDetails($want2_id)->toArray()) . PHP_EOL;
echo "GET WANT 3: " . json_encode(WantsDatabase::getWantRequestDetails($want3_id)->toArray()) . PHP_EOL;


// --- PROFILE & USER LISTINGS TESTS ---
$updatedUser = json_encode(['userID' => $u1_id, 'name' => 'Yamkela', 'surname' => 'Jackson', 'username' => $u1_uname . '_upd', 'email' => $u1_email, 'password' => 'newpass@123']);
$updateProfile = UserProfile::updateProfile($updatedUser);
echo "UPDATE PROFILE: " . $updateProfile->getMessage() . PHP_EOL;

// Get Profiles
echo "GET PROFILE 1: " . json_encode(UserProfile::getUserProfile(json_encode(['userID' => $u1_id]))->toArray()) . PHP_EOL;
echo "GET PROFILE 2: " . json_encode(UserProfile::getUserProfile(json_encode(['userID' => $u2_id]))->toArray()) . PHP_EOL;
echo "GET PROFILE 3: " . json_encode(UserProfile::getUserProfile(json_encode(['userID' => $u3_id]))->toArray()) . PHP_EOL;

// Get User Data
echo "GET LISTINGS 1: " . json_encode(UserListings::getUserListings($u1_id)->toArray()) . PHP_EOL;
echo "GET LISTINGS 2: " . json_encode(UserListings::getUserListings($u2_id)->toArray()) . PHP_EOL;
echo "GET LISTINGS 3: " . json_encode(UserListings::getUserListings($u3_id)->toArray()) . PHP_EOL;

echo "GET WANT REQUESTS 1: " . json_encode(WantRequest::getUserWantRequests($u1_id)->toArray()) . PHP_EOL;
echo "GET WANT REQUESTS 2: " . json_encode(WantRequest::getUserWantRequests($u2_id)->toArray()) . PHP_EOL;
echo "GET WANT REQUESTS 3: " . json_encode(WantRequest::getUserWantRequests($u3_id)->toArray()) . PHP_EOL;

echo "\n=== TESTS COMPLETED ===\n";