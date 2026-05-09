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
require_once __DIR__ . '/api/chat/messages.php';

// Generate Unique User Variables
$u1_id = uniqid('user_'); $u1_email = $u1_id . '@test.com'; $u1_uname = 'u_' . uniqid();
$u2_id = uniqid('user_'); $u2_email = $u2_id . '@test.com'; $u2_uname = 'u_' . uniqid();
$u3_id = uniqid('user_'); $u3_email = $u3_id . '@test.com'; $u3_uname = 'u_' . uniqid();

$password = 'Test@1234';

echo "=== STARTING MOJA MARKET API TESTS ===\n\n";

// --- AUTH TESTS ---
$registerJson1 = json_encode(['userID' => $u1_id, 'name' => 'Yamkela', 'surname' => 'Jack', 'username' => $u1_uname, 'email' => $u1_email, 'password' => $password]);
$register1 = Register::registerUser($registerJson1);
echo "REGISTER 1: " . $register1->getMessage() . PHP_EOL;

$registerJson2 = json_encode(['userID' => $u2_id, 'name' => 'Lebo', 'surname' => 'Mokoena', 'username' => $u2_uname, 'email' => $u2_email, 'password' => $password]);
$register2 = Register::registerUser($registerJson2);
echo "REGISTER 2: " . $register2->getMessage() . PHP_EOL;

$registerJson3 = json_encode(['userID' => $u3_id, 'name' => 'Sipho', 'surname' => 'Dlamini', 'username' => $u3_uname, 'email' => $u3_email, 'password' => $password]);
$register3 = Register::registerUser($registerJson3);
echo "REGISTER 3: " . $register3->getMessage() . PHP_EOL;

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

echo "GET WANT 1: " . json_encode(WantsDatabase::getWantRequestDetails($want1_id)->toArray()) . PHP_EOL;
echo "GET WANT 2: " . json_encode(WantsDatabase::getWantRequestDetails($want2_id)->toArray()) . PHP_EOL;
echo "GET WANT 3: " . json_encode(WantsDatabase::getWantRequestDetails($want3_id)->toArray()) . PHP_EOL;

// --- PROFILE & USER LISTINGS TESTS ---
$updatedUser = json_encode(['userID' => $u1_id, 'name' => 'Yamkela', 'surname' => 'Jackson', 'username' => $u1_uname . '_upd', 'email' => $u1_email, 'password' => 'newpass@123']);
$updateProfile = UserProfile::updateProfile($updatedUser);
echo "UPDATE PROFILE: " . $updateProfile->getMessage() . PHP_EOL;

echo "GET PROFILE 1: " . json_encode(UserProfile::getUserProfile(json_encode(['userID' => $u1_id]))->toArray()) . PHP_EOL;
echo "GET PROFILE 2: " . json_encode(UserProfile::getUserProfile(json_encode(['userID' => $u2_id]))->toArray()) . PHP_EOL;
echo "GET PROFILE 3: " . json_encode(UserProfile::getUserProfile(json_encode(['userID' => $u3_id]))->toArray()) . PHP_EOL;

echo "GET LISTINGS 1: " . json_encode(UserListings::getUserListings($u1_id)->toArray()) . PHP_EOL;
echo "GET LISTINGS 2: " . json_encode(UserListings::getUserListings($u2_id)->toArray()) . PHP_EOL;
echo "GET LISTINGS 3: " . json_encode(UserListings::getUserListings($u3_id)->toArray()) . PHP_EOL;

echo "GET WANT REQUESTS 1: " . json_encode(WantRequest::getUserWantRequests($u1_id)->toArray()) . PHP_EOL;
echo "GET WANT REQUESTS 2: " . json_encode(WantRequest::getUserWantRequests($u2_id)->toArray()) . PHP_EOL;
echo "GET WANT REQUESTS 3: " . json_encode(WantRequest::getUserWantRequests($u3_id)->toArray()) . PHP_EOL;

echo "\n=== TESTS COMPLETED ===\n";

// -------------------------
// --- CHAT TESTS ---
// -------------------------

echo "\n=== CHAT TESTS ===\n";

$chat1_id = uniqid('chat_');
$chat2_id = uniqid('chat_');
$chat3_id = uniqid('chat_');

// u2 contacts u1 about the Samsung S21 Ultra (item chat)
$createChat1 = Messages::createChat(json_encode([
    'chatID' => $chat1_id,
    'user1'  => $u2_id,
    'user2'  => $u1_id,
    'itemID' => $item1_id,
    'wantID' => null
]));
echo "CREATE CHAT 1 (u2 -> u1, Samsung): " . $createChat1->getMessage() . PHP_EOL;

// u3 contacts u2 about the Nike Air Max (item chat)
$createChat2 = Messages::createChat(json_encode([
    'chatID' => $chat2_id,
    'user1'  => $u3_id,
    'user2'  => $u2_id,
    'itemID' => $item2_id,
    'wantID' => null
]));
echo "CREATE CHAT 2 (u3 -> u2, Nike Air Max): " . $createChat2->getMessage() . PHP_EOL;

// u2 responds to u1's MacBook Pro M2 want request (want chat)
$createChat3 = Messages::createChat(json_encode([
    'chatID' => $chat3_id,
    'user1'  => $u2_id,
    'user2'  => $u1_id,
    'itemID' => null,
    'wantID' => $want1_id
]));
echo "CREATE CHAT 3 (u2 -> u1, MacBook want): " . $createChat3->getMessage() . PHP_EOL;

// Duplicate check — same users + same item should return existing chat, not create a new one
$dupChat = Messages::createChat(json_encode([
    'chatID' => uniqid('chat_'),
    'user1'  => $u2_id,
    'user2'  => $u1_id,
    'itemID' => $item1_id,
    'wantID' => null
]));
echo "CREATE CHAT DUPLICATE (should return existing): " . $dupChat->getMessage() . PHP_EOL;

// No item or want — should fail
$invalidChat = Messages::createChat(json_encode([
    'chatID' => uniqid('chat_'),
    'user1'  => $u1_id,
    'user2'  => $u3_id,
    'itemID' => null,
    'wantID' => null
]));
echo "CREATE CHAT INVALID (no item or want): " . $invalidChat->getMessage() . PHP_EOL;

echo "\n--- CHAT 1: u2 & u1 discussing Samsung S21 Ultra ---\n";

// Simulate opening the chat screen — load full history first (empty at this point)
$lastTime1 = '2000-01-01 00:00:00';
$history1 = Messages::getChatHistory($chat1_id);
echo "CHAT HISTORY (on open, should be empty): " . json_encode($history1->toArray()) . PHP_EOL;

// Poll 1 — no messages yet
$poll1 = Messages::getNewMessages($chat1_id, $lastTime1);
echo "POLL 1 (no messages yet): " . json_encode($poll1->toArray()) . PHP_EOL;

// u2 sends first message
$msg1 = Messages::sendMessage(json_encode([
    'messageID' => uniqid('msg_'),
    'chatID'    => $chat1_id,
    'senderID'  => $u2_id,
    'message'   => 'Hi, is the Samsung S21 Ultra still available?'
]));
echo "SEND MSG 1: " . $msg1->getMessage() . PHP_EOL;

// Poll 2 — u1 picks up u2's message
$poll2 = Messages::getNewMessages($chat1_id, $lastTime1);
echo "POLL 2 (u1 sees u2 message): " . json_encode($poll2->toArray()) . PHP_EOL;

// Update lastTime to the time of the last received message
$poll2Data = $poll2->getData();
if (!empty($poll2Data)) {
    $lastTime1 = end($poll2Data)['time_sent'];
}

// u1 replies
$msg2 = Messages::sendMessage(json_encode([
    'messageID' => uniqid('msg_'),
    'chatID'    => $chat1_id,
    'senderID'  => $u1_id,
    'message'   => 'Yes, still available! R9500 firm.'
]));
echo "SEND MSG 2: " . $msg2->getMessage() . PHP_EOL;

// Poll 3 — u2 picks up u1's reply
$poll3 = Messages::getNewMessages($chat1_id, $lastTime1);
echo "POLL 3 (u2 sees u1 reply): " . json_encode($poll3->toArray()) . PHP_EOL;

if (!empty($poll3->getData())) {
    $lastTime1 = end($poll3->getData())['time_sent'];
}

// u2 negotiates
$msg3 = Messages::sendMessage(json_encode([
    'messageID' => uniqid('msg_'),
    'chatID'    => $chat1_id,
    'senderID'  => $u2_id,
    'message'   => 'Would you take R8500? I can collect today.'
]));
echo "SEND MSG 3: " . $msg3->getMessage() . PHP_EOL;

// Poll 4 — u1 picks up negotiation
$poll4 = Messages::getNewMessages($chat1_id, $lastTime1);
echo "POLL 4 (u1 sees negotiation): " . json_encode($poll4->toArray()) . PHP_EOL;

if (!empty($poll4->getData())) {
    $lastTime1 = end($poll4->getData())['time_sent'];
}

// u1 agrees
$msg4 = Messages::sendMessage(json_encode([
    'messageID' => uniqid('msg_'),
    'chatID'    => $chat1_id,
    'senderID'  => $u1_id,
    'message'   => 'Deal! Come to Sandton City at 2pm.'
]));
echo "SEND MSG 4: " . $msg4->getMessage() . PHP_EOL;

// Poll 5 — u2 sees the deal confirmed
$poll5 = Messages::getNewMessages($chat1_id, $lastTime1);
echo "POLL 5 (u2 sees deal): " . json_encode($poll5->toArray()) . PHP_EOL;

// Full history after conversation
echo "FULL CHAT 1 HISTORY: " . json_encode(Messages::getChatHistory($chat1_id)->toArray()) . PHP_EOL;

echo "\n--- CHAT 2: u3 & u2 discussing Nike Air Max ---\n";

$lastTime2 = '2000-01-01 00:00:00';

$msg5 = Messages::sendMessage(json_encode([
    'messageID' => uniqid('msg_'),
    'chatID'    => $chat2_id,
    'senderID'  => $u3_id,
    'message'   => 'Hey are these still available in size 10?'
]));
echo "SEND MSG 5: " . $msg5->getMessage() . PHP_EOL;

$poll6 = Messages::getNewMessages($chat2_id, $lastTime2);
echo "POLL 6 (u2 sees u3 message): " . json_encode($poll6->toArray()) . PHP_EOL;

if (!empty($poll6->getData())) {
    $lastTime2 = end($poll6->getData())['time_sent'];
}

$msg6 = Messages::sendMessage(json_encode([
    'messageID' => uniqid('msg_'),
    'chatID'    => $chat2_id,
    'senderID'  => $u2_id,
    'message'   => 'Yes! Only worn twice. R1800.'
]));
echo "SEND MSG 6: " . $msg6->getMessage() . PHP_EOL;

$poll7 = Messages::getNewMessages($chat2_id, $lastTime2);
echo "POLL 7 (u3 sees u2 reply): " . json_encode($poll7->toArray()) . PHP_EOL;

echo "\n--- CHAT 3: u2 & u1 discussing MacBook Pro M2 want request ---\n";

$lastTime3 = '2000-01-01 00:00:00';

$msg7 = Messages::sendMessage(json_encode([
    'messageID' => uniqid('msg_'),
    'chatID'    => $chat3_id,
    'senderID'  => $u2_id,
    'message'   => 'Hi, I have a MacBook Pro M2 16GB. Would that work for you?'
]));
echo "SEND MSG 7: " . $msg7->getMessage() . PHP_EOL;

$poll8 = Messages::getNewMessages($chat3_id, $lastTime3);
echo "POLL 8 (u1 sees u2 offer): " . json_encode($poll8->toArray()) . PHP_EOL;

if (!empty($poll8->getData())) {
    $lastTime3 = end($poll8->getData())['time_sent'];
}

$msg8 = Messages::sendMessage(json_encode([
    'messageID' => uniqid('msg_'),
    'chatID'    => $chat3_id,
    'senderID'  => $u1_id,
    'message'   => 'Perfect, that is exactly what I need. What is your price?'
]));
echo "SEND MSG 8: " . $msg8->getMessage() . PHP_EOL;

$poll9 = Messages::getNewMessages($chat3_id, $lastTime3);
echo "POLL 9 (u2 sees u1 interest): " . json_encode($poll9->toArray()) . PHP_EOL;

// Get all chats for each user (the chat list screen)
echo "\n--- USER CHAT LISTS ---\n";
echo "GET CHATS FOR U1: " . json_encode(Messages::getUserChats($u1_id)->toArray()) . PHP_EOL;
echo "GET CHATS FOR U2: " . json_encode(Messages::getUserChats($u2_id)->toArray()) . PHP_EOL;
echo "GET CHATS FOR U3: " . json_encode(Messages::getUserChats($u3_id)->toArray()) . PHP_EOL;

echo "\n=== CHAT TESTS COMPLETED ===\n";