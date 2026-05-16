<?php
/**
 * Console Test Suite
 * Run with: php test.php
 *
 * Covers: Register, Login, Items, Wants, Chat/Messages, User Profile, Listings, Want Requests
 */

// ── Bootstrap ────────────────────────────────────────────────────────────────
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
require_once __DIR__ . '/api/chat/messages.php';

// ── Helpers ───────────────────────────────────────────────────────────────────
$passed = 0;
$failed = 0;

function check(string $label, Response $response, bool $expectSuccess): void {
    global $passed, $failed;
    $ok   = $response->getSuccess() === $expectSuccess;
    $icon = $ok ? '✅' : '❌';
    $flag = $expectSuccess ? '[SHOULD PASS]' : '[SHOULD FAIL]';
    echo "$icon $flag $label → " . $response->getMessage() . PHP_EOL;
    $ok ? $passed++ : $failed++;
}

function section(string $title): void {
    echo PHP_EOL . str_repeat('─', 60) . PHP_EOL;
    echo "  $title" . PHP_EOL;
    echo str_repeat('─', 60) . PHP_EOL;
}

// ── Seed IDs (unique per run) ─────────────────────────────────────────────────
$uid      = uniqid();
$userID1  = 'user_a_' . $uid;
$userID2  = 'user_b_' . $uid;
$username1 = 'alice_' . $uid;
$username2 = 'bob_'   . $uid;
$email1   = $userID1  . '@test.com';
$email2   = $userID2  . '@test.com';
$itemID   = 'item_'   . $uid;
$wantID   = 'want_'   . $uid;
$chatID   = 'chat_'   . $uid;
$msgID1   = 'msg1_'   . $uid;
$msgID2   = 'msg2_'   . $uid;

// ═════════════════════════════════════════════════════════════════════════════
// 1. REGISTER
// ═════════════════════════════════════════════════════════════════════════════
section('1. REGISTER');

check('Register user 1', Register::registerUser(json_encode([
    'userID'   => $userID1,
    'name'     => 'Alice',
    'surname'  => 'Smith',
    'username' => $username1,
    'email'    => $email1,
    'password' => 'Pass@1234',
])), true);

check('Register user 2', Register::registerUser(json_encode([
    'userID'   => $userID2,
    'name'     => 'Bob',
    'surname'  => 'Jones',
    'username' => $username2,
    'email'    => $email2,
    'password' => 'Pass@5678',
])), true);

check('Duplicate username', Register::registerUser(json_encode([
    'userID'   => 'dup_' . $uid,
    'name'     => 'Dup',
    'surname'  => 'User',
    'username' => $username1,       // same as user 1
    'email'    => 'dup_' . $uid . '@test.com',
    'password' => 'Pass@0000',
])), false);

check('Duplicate email', Register::registerUser(json_encode([
    'userID'   => 'dup2_' . $uid,
    'name'     => 'Dup',
    'surname'  => 'User',
    'username' => 'unique_' . $uid,
    'email'    => $email1,          // same as user 1
    'password' => 'Pass@0000',
])), false);

// ═════════════════════════════════════════════════════════════════════════════
// 2. LOGIN
// ═════════════════════════════════════════════════════════════════════════════
section('2. LOGIN');

check('Login with username',    Login::login($username1, 'Pass@1234'), true);
check('Login with email',       Login::login($email1,    'Pass@1234'), true);
check('Wrong password',         Login::login($username1, 'wrong'),     false);
check('Non-existent user',      Login::login('ghost_'.$uid, 'x'),      false);

// ═════════════════════════════════════════════════════════════════════════════
// 3. USER PROFILE
// ═════════════════════════════════════════════════════════════════════════════
section('3. USER PROFILE');

check('Get profile (valid)',   UserProfile::getUserProfile(json_encode(['userID' => $userID1])), true);
check('Get profile (unknown)', UserProfile::getUserProfile(json_encode(['userID' => 'nobody'])), false);

$newUsername = 'alice_upd_' . $uid;
$newEmail    = 'alice_upd_' . $uid . '@test.com';

check('Update with taken username',
    UserProfile::updateProfile(json_encode([
        'userID'   => $userID1,
        'name'     => 'Alice', 'surname' => 'Smith',
        'username' => $username2,   // belongs to user 2
        'email'    => $newEmail,
        'password' => 'Pass@1234',
    ])), false);

check('Update with taken email',
    UserProfile::updateProfile(json_encode([
        'userID'   => $userID1,
        'name'     => 'Alice', 'surname' => 'Smith',
        'username' => $newUsername,
        'email'    => $email2,       // belongs to user 2
        'password' => 'Pass@1234',
    ])), false);

check('Update with own details (no conflict)',
    UserProfile::updateProfile(json_encode([
        'userID'   => $userID1,
        'name'     => 'Alice', 'surname' => 'Smith',
        'username' => $username1,
        'email'    => $email1,
        'password' => 'Pass@1234',
    ])), true);

check('Update with genuinely new details',
    UserProfile::updateProfile(json_encode([
        'userID'   => $userID1,
        'name'     => 'Alice', 'surname' => 'Smith',
        'username' => $newUsername,
        'email'    => $newEmail,
        'password' => 'NewPass@99',
    ])), true);

// ═════════════════════════════════════════════════════════════════════════════
// 4. ITEMS
// ═════════════════════════════════════════════════════════════════════════════
section('4. ITEMS');

$itemPayload = [
    'itemID'          => $itemID,
    'seller'          => ['userID' => $userID1],
    'datePosted'      => date('Y-m-d'),
    'itemName'        => 'Test Widget',
    'itemDescription' => 'A widget for testing',
    'condition'       => 'New',
    'sellerLocation'  => 'Johannesburg',
    'stockStatus'     => 'Available',
    'quantity'        => 5,
    'price'           => 99.99,
    'itemImage'       => ['imageID' => 'img_' . $uid, 'imagePath' => 'https://via.placeholder.com/300'],
];

check('Post item',              ItemDatabase::postItem(json_encode($itemPayload)), true);
check('Post duplicate item',    ItemDatabase::postItem(json_encode($itemPayload)), false);
check('Get item details',       ItemDatabase::getItemDetails($itemID),             true);
check('Get item (invalid ID)',  ItemDatabase::getItemDetails('nonexistent_id'),    false);
check('Get feed',               ItemDatabase::getFeed(),                           true);

$itemPayload['itemName'] = 'Updated Widget';
$itemPayload['price']    = 149.99;
check('Update item', ItemDatabase::updateItem(json_encode($itemPayload)), true);

check('Update non-existent item',
    ItemDatabase::updateItem(json_encode(array_merge($itemPayload, ['itemID' => 'ghost_item']))),
    false);

// ═════════════════════════════════════════════════════════════════════════════
// 5. WANTS
// ═════════════════════════════════════════════════════════════════════════════
section('5. WANTS');

$wantPayload = [
    'id'          => $wantID,
    'buyer'       => ['userID' => $userID2],
    'datePosted'  => date('Y-m-d'),
    'item'        => 'Vintage Camera',
    'description' => 'Looking for a retro film camera',
    'budget'      => 500,
    'wantStatus'  => 'Open',
];

check('Post want request',           WantsDatabase::postWantRequest(json_encode($wantPayload)), true);
check('Post duplicate want request', WantsDatabase::postWantRequest(json_encode($wantPayload)), false);
check('Get want details',            WantsDatabase::getWantRequestDetails($wantID),             true);
check('Get want feed',               (new WantsDatabase())->getWantRequestFeed(),               true);

$wantPayload['budget']     = 600;
$wantPayload['wantStatus'] = 'Closed';
check('Update want request', WantsDatabase::updateWantRequest(json_encode($wantPayload)), true);

check('Update non-existent want',
    WantsDatabase::updateWantRequest(json_encode(array_merge($wantPayload, ['id' => 'ghost_want']))),
    false);

// ═════════════════════════════════════════════════════════════════════════════
// 6. USER LISTINGS & WANT REQUESTS
// ═════════════════════════════════════════════════════════════════════════════
section('6. USER LISTINGS & WANT REQUESTS');

check('Get listings for user 1',         UserListings::getUserListings($userID1), true);
check('Get want requests for user 2',    WantRequest::getUserWantRequests($userID2), true);
check('Get listings for unknown user',   UserListings::getUserListings('nobody_' . $uid), false);

// ═════════════════════════════════════════════════════════════════════════════
// 7. CHAT & MESSAGES
// ═════════════════════════════════════════════════════════════════════════════
section('7. CHAT & MESSAGES');

check('Create chat (item)',
    Messages::createChat(json_encode([
        'chatID' => $chatID,
        'user1'  => $userID1,
        'user2'  => $userID2,
        'itemID' => $itemID,
    ])), true);

check('Create duplicate chat (returns existing)',
    Messages::createChat(json_encode([
        'chatID' => 'chat2_' . $uid,    // new ID but same pair + item
        'user1'  => $userID1,
        'user2'  => $userID2,
        'itemID' => $itemID,
    ])), true);   // should still succeed, returning the original chatID

check('Create chat without itemID or wantID',
    Messages::createChat(json_encode([
        'chatID' => 'bad_chat_' . $uid,
        'user1'  => $userID1,
        'user2'  => $userID2,
    ])), false);

check('Send message 1',
    Messages::sendMessage(json_encode([
        'messageID' => $msgID1,
        'chatID'    => $chatID,
        'senderID'  => $userID1,
        'message'   => "Hey Bob, is this still available?",
    ])), true);

check('Send message 2',
    Messages::sendMessage(json_encode([
        'messageID' => $msgID2,
        'chatID'    => $chatID,
        'senderID'  => $userID2,
        'message'   => "Yes it is! Make me an offer.",
    ])), true);

check('Get chat history',   Messages::getChatHistory($chatID),                    true);
check('Get new messages',   Messages::getNewMessages($chatID, '2000-01-01 00:00:00'), true);
check('Get user chats (1)', Messages::getUserChats($userID1),                     true);

// ═════════════════════════════════════════════════════════════════════════════
// 8. DELETE CLEANUP
// ═════════════════════════════════════════════════════════════════════════════
section('8. DELETE CLEANUP');

check('Delete item',
    ItemDatabase::deleteItem(json_encode(['itemID' => $itemID])), true);

check('Delete item again (already gone)',
    ItemDatabase::deleteItem(json_encode(['itemID' => $itemID])), false);

check('Delete want request',
    WantsDatabase::deleteWantRequest($wantID), true);

check('Delete want again (already gone)',
    WantsDatabase::deleteWantRequest($wantID), false);

// ═════════════════════════════════════════════════════════════════════════════
// SUMMARY
// ═════════════════════════════════════════════════════════════════════════════
$total = $passed + $failed;
echo PHP_EOL . str_repeat('═', 60) . PHP_EOL;
echo "  RESULTS: $passed / $total passed";
if ($failed > 0) {
    echo "  ($failed UNEXPECTED)";
}
echo PHP_EOL . str_repeat('═', 60) . PHP_EOL;