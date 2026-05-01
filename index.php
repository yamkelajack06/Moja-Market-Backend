<?php
    require_once __DIR__ . '/config/db.php';
    require_once __DIR__ . '/api/auth/login.php';
    require_once __DIR__ . '/api/auth/register.php';
    require_once __DIR__ . '/api/posts/items.php';
    require_once __DIR__ . '/api/posts/wants.php';
    require_once __DIR__ . '/api/user/profile.php';
    require_once __DIR__ . '/api/user/listings.php';
    require_once __DIR__ . '/api/user/want_requests.php';

//test register user 1
$registerJson = json_encode([
    'userID'   => 'user_99999',
    'name'     => 'Yamkela',
    'surname'  => 'Jack',
    'username' => 'jack1011',
    'email'    => 'yamkelajack101@gmail.com',
    'password' => 'jack101@123'
]);

$register = Register::registerUser($registerJson);
echo "REGISTER: " . $register->getMessage() . PHP_EOL;

//test register user 2
$registerJson2 = json_encode([
    'userID'   => 'user_88888',
    'name'     => 'Lebo',
    'surname'  => 'Mokoena',
    'username' => 'lebo_m',
    'email'    => 'lebo@gmail.com',
    'password' => 'lebo@123'
]);

$register2 = Register::registerUser($registerJson2);
echo "REGISTER: " . $register2->getMessage() . PHP_EOL;

//test register user 3
$registerJson3 = json_encode([
    'userID'   => 'user_77777',
    'name'     => 'Sipho',
    'surname'  => 'Dlamini',
    'username' => 'sipho_d',
    'email'    => 'sipho@gmail.com',
    'password' => 'sipho@123'
]);

$register3 = Register::registerUser($registerJson3);
echo "REGISTER: " . $register3->getMessage() . PHP_EOL;

//test login user 1
$login = Login::login('yamkelajack101@gmail.com', 'jack101@123');
echo "LOGIN: " . $login->getMessage() . PHP_EOL;

//test login user 2
$login2 = Login::login('lebo@gmail.com', 'lebo@123');
echo "LOGIN: " . $login2->getMessage() . PHP_EOL;

//test login user 3
$login3 = Login::login('sipho@gmail.com', 'sipho@123');
echo "LOGIN: " . $login3->getMessage() . PHP_EOL;

//test post item 1
$item1 = json_encode([
    'itemID' => 'item_111111',
    'seller' => [
        'userID' => 'user_99999'
    ],
    'datePosted' => '2026-04-30 20:00:00',
    'itemName' => 'Samsung Galaxy S21',
    'itemDescription' => 'Lightly used, no cracks, everything works perfectly',
    'condition' => 'Used',
    'sellerLocation' => 'Johannesburg',
    'stockStatus' => 'In Stock',
    'quantity' => 1,
    'price' => 7500,
    'averageRating' => 0,
    'itemImage' => [
        'imageID' => 'image_111111',
        'imagePath' => 's21.jpg'
    ]
]);

$post1 = ItemDatabase::postItem($item1);
echo "POST ITEM: " . $post1->getMessage() . PHP_EOL;

//test post item 2
$item2 = json_encode([
    'itemID' => 'item_222222',
    'seller' => [
        'userID' => 'user_88888'
    ],
    'datePosted' => '2026-04-30 21:00:00',
    'itemName' => 'Nike Air Max 90',
    'itemDescription' => 'Size 10, worn twice, still clean',
    'condition' => 'Like New',
    'sellerLocation' => 'Pretoria',
    'stockStatus' => 'In Stock',
    'quantity' => 1,
    'price' => 1800,
    'averageRating' => 0,
    'itemImage' => [
        'imageID' => 'image_222222',
        'imagePath' => 'airmax.jpg'
    ]
]);

$post2 = ItemDatabase::postItem($item2);
echo "POST ITEM: " . $post2->getMessage() . PHP_EOL;

//test post item 3
$item3 = json_encode([
    'itemID' => 'item_333333',
    'seller' => [
        'userID' => 'user_77777'
    ],
    'datePosted' => '2026-04-30 22:00:00',
    'itemName' => 'PS5 Controller',
    'itemDescription' => 'Brand new sealed in box',
    'condition' => 'New',
    'sellerLocation' => 'Sandton',
    'stockStatus' => 'In Stock',
    'quantity' => 2,
    'price' => 1200,
    'averageRating' => 0,
    'itemImage' => [
        'imageID' => 'image_333333',
        'imagePath' => 'ps5controller.jpg'
    ]
]);

$post3 = ItemDatabase::postItem($item3);
echo "POST ITEM: " . $post3->getMessage() . PHP_EOL;

//test update item
$newItem = json_encode([
    'itemID' => 'item_111111',
    'seller' => [
        'userID' => 'user_99999'
    ],
    'datePosted' => '2026-04-30 20:10:00',
    'itemName' => 'Samsung Galaxy S21 Ultra',
    'itemDescription' => 'Updated listing, includes case and charger',
    'condition' => 'Like New',
    'sellerLocation' => 'Sandton',
    'stockStatus' => 'In Stock',
    'quantity' => 1,
    'price' => 9500,
    'averageRating' => 4.9,
    'itemImage' => [
        'imageID' => 'image_111111',
        'imagePath' => 's21ultra.jpg'
    ]
]);

$update = ItemDatabase::updateItem($newItem);
echo "UPDATE ITEM: " . $update->getMessage() . PHP_EOL;

//test get item 1
$getDetails1 = ItemDatabase::getItemDetails('item_111111');
echo "GET ITEM: " . json_encode($getDetails1) . PHP_EOL;

//test get item 2
$getDetails2 = ItemDatabase::getItemDetails('item_222222');
echo "GET ITEM: " . json_encode($getDetails2) . PHP_EOL;

//test get item 3
$getDetails3 = ItemDatabase::getItemDetails('item_333333');
echo "GET ITEM: " . json_encode($getDetails3) . PHP_EOL;

//test get feed
$feed = ItemDatabase::getFeed();
echo "GET FEED: " . json_encode($feed) . PHP_EOL;

//test delete item
// $delete = ItemDatabase::deleteItem('item_111111');
// echo "DELETE ITEM: " . $delete->getMessage() . PHP_EOL;

//test post want 1
$want1 = json_encode([
    'id' => 'want_111111',
    'buyer' => [
        'userID' => 'user_99999'
    ],
    'datePosted' => '2026-04-30 20:00:00',
    'item' => 'JBL Speakers',
    'description' => 'Looking for pre owned JBL speakers',
    'budget' => 1200,
    'wantStatus' => true
]);

$postWant1 = WantsDatabase::postWantRequest($want1);
echo "POST WANT: " . $postWant1->getMessage() . PHP_EOL;

//test post want 2
$want2 = json_encode([
    'id' => 'want_222222',
    'buyer' => [
        'userID' => 'user_88888'
    ],
    'datePosted' => '2026-04-30 21:00:00',
    'item' => 'iPhone 13',
    'description' => 'Any colour, good condition',
    'budget' => 8000,
    'wantStatus' => true
]);

$postWant2 = WantsDatabase::postWantRequest($want2);
echo "POST WANT: " . $postWant2->getMessage() . PHP_EOL;

//test post want 3
$want3 = json_encode([
    'id' => 'want_333333',
    'buyer' => [
        'userID' => 'user_77777'
    ],
    'datePosted' => '2026-04-30 22:00:00',
    'item' => 'Gaming Chair',
    'description' => 'Preferably ergonomic, good condition',
    'budget' => 3000,
    'wantStatus' => true
]);

$postWant3 = WantsDatabase::postWantRequest($want3);
echo "POST WANT: " . $postWant3->getMessage() . PHP_EOL;

//test update want
$newWant = json_encode([
    'id' => 'want_111111',
    'buyer' => [
        'userID' => 'user_99999'
    ],
    'datePosted' => '2026-04-30 20:10:00',
    'item' => 'JBL Speakers Xtreme',
    'description' => 'Updated - looking for JBL Xtreme specifically',
    'budget' => 1500,
    'wantStatus' => true
]);

$updateWant = WantsDatabase::updateWantRequest($newWant);
echo "UPDATE WANT: " . $updateWant->getMessage() . PHP_EOL;

//test get want 1
$getWant1 = WantsDatabase::getWantRequestDetails('want_111111');
echo "GET WANT: " . json_encode($getWant1) . PHP_EOL;

//test get want 2
$getWant2 = WantsDatabase::getWantRequestDetails('want_222222');
echo "GET WANT: " . json_encode($getWant2) . PHP_EOL;

//test get want 3
$getWant3 = WantsDatabase::getWantRequestDetails('want_333333');
echo "GET WANT: " . json_encode($getWant3) . PHP_EOL;

//test delete want
// $deleteWant = WantsDatabase::deleteWant('want_111111');
// echo "DELETE WANT: " . $deleteWant->getMessage() . PHP_EOL;

//test get user profile 1
$profile1 = UserProfile::getUserProfile(json_encode(['userID' => 'user_99999']));
echo "GET PROFILE: " . json_encode($profile1) . PHP_EOL;

//test get user profile 2
$profile2 = UserProfile::getUserProfile(json_encode(['userID' => 'user_88888']));
echo "GET PROFILE: " . json_encode($profile2) . PHP_EOL;

//test get user profile 3
$profile3 = UserProfile::getUserProfile(json_encode(['userID' => 'user_77777']));
echo "GET PROFILE: " . json_encode($profile3) . PHP_EOL;

//test update user profile
$updatedUser = json_encode([
    'userID'   => 'user_99999',
    'name'     => 'Yamkela',
    'surname'  => 'Jackson',
    'username' => 'jack1011_updated',
    'email'    => 'yamkela_updated@gmail.com',
    'password' => 'newpass@123'
]);

$updateProfile = UserProfile::updateProfile($updatedUser);
echo "UPDATE PROFILE: " . $updateProfile->getMessage() . PHP_EOL;

//test get user listings 1
$listings1 = UserListings::getUserListings('user_99999');
echo "GET LISTINGS: " . json_encode($listings1) . PHP_EOL;

//test get user listings 2
$listings2 = UserListings::getUserListings('user_88888');
echo "GET LISTINGS: " . json_encode($listings2) . PHP_EOL;

//test get user listings 3
$listings3 = UserListings::getUserListings('user_77777');
echo "GET LISTINGS: " . json_encode($listings3) . PHP_EOL;

//test get user want requests 1
$wantRequests1 = WantRequest::getUserWantRequests('user_99999');
echo "GET WANT REQUESTS: " . json_encode($wantRequests1) . PHP_EOL;

//test get user want requests 2
$wantRequests2 = WantRequest::getUserWantRequests('user_88888');
echo "GET WANT REQUESTS: " . json_encode($wantRequests2) . PHP_EOL;

//test get user want requests 3
$wantRequests3 = WantRequest::getUserWantRequests('user_77777');
echo "GET WANT REQUESTS: " . json_encode($wantRequests3) . PHP_EOL;