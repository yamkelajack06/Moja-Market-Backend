<?php
   require_once __DIR__ . '/config/db.php';
   require_once __DIR__ . '/api/auth/login.php';
   require_once __DIR__ . '/api/auth/register.php';
   require_once __DIR__ . '/api/posts/items.php';
   require_once __DIR__ . '/api/posts/wants.php';

//test register
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

//test login
$login = Login::login('yamkelajack101@gmail.com', 'jack101@123');
echo "LOGIN: " . $login->getMessage() . PHP_EOL;

//test post item
$Item = json_encode([
    'itemID' => 'item_123456',
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
    'averageRating' => 4.6,
    'itemImage' => [
    'imageID' => 'image_123456',
    'imagePath' => 's21.jpg'
]
]);

$post = ItemDatabase::postItem($Item);
echo "POST ITEM: " . $post->getMessage() . PHP_EOL;

//test update item
$newItem = json_encode([
    'itemID' => 'item_123456',
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
    'imageID' => 'image_123456',
    'imagePath' => 's21.jpg'
   ]
]);

$update = ItemDatabase::updateItem($newItem);
echo "UPDATE: " . $update->getMessage() . PHP_EOL;

//test get item
$itemID = 'item_123456';

$getDetails = ItemDatabase::getItemDetails($itemID);
echo "GET: " . json_encode($getDetails) . PHP_EOL;

//test delete
// $delete = ItemDatabase::deleteItem($itemID);
// echo "DELETE: " . $delete->getMessage() . PHP_EOL;

//test post want
$want = json_encode([
    'id' => 'want_123456',
    'buyer' => [
        'userID' => 'user_99999'
    ],
    'datePosted' => '2026-04-30 20:00:00',
    'item' => 'JBL Speakers',
    'description' => 'Looking for pre owned JBL speakers',
    'budget' => 1200,
    'wantStatus' => true
]);

$postWant = WantsDatabase::postWantRequest($want);
echo "POST WANT: " . $postWant->getMessage() . PHP_EOL;

//test update want
$newWant = json_encode([
    'id' => 'want_123456',
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

//test get want
$wantID = 'want_123456';

$getWant = WantsDatabase::getWantRequestDetails($wantID);
echo "GET WANT: " . json_encode($getWant) . PHP_EOL;

//test delete want
// $deleteWant = WantsDatabase::deleteWant($wantID);
// echo "DELETE WANT: " . $deleteWant->getMessage() . PHP_EOL;