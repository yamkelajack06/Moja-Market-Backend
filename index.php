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
require_once __DIR__ . '/api/upload/upload_image.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle option request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$path   = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$body   = file_get_contents('php://input');

switch ($path) {

    // Auth
    case 'api/auth/register':
        echo json_encode(Register::registerUser($body));
        break;

    case 'api/auth/login':
        $data = json_decode($body, true);
        $res  = Login::login($data['loginID'], $data['password']);
        echo json_encode(['success' => $res->getSuccess(), 'message' => $res->getMessage()]);
        break;

    // Items
    case 'api/posts/items':
        if ($method === 'POST')   echo json_encode(ItemDatabase::postItem($body));
        if ($method === 'PUT')    echo json_encode(ItemDatabase::updateItem($body));
        if ($method === 'DELETE') echo json_encode(ItemDatabase::deleteItem($body));
        break;

    case 'api/posts/feed':
        echo ItemDatabase::getFeed();
        break;

    case 'api/posts/item':
        $itemID = $_GET['id'] ?? '';
        echo ItemDatabase::getItemDetails($itemID);
        break;

    // Wants
    case 'api/posts/wants':
        if ($method === 'POST') echo json_encode(WantsDatabase::postWantRequest($body));
        if ($method === 'PUT') echo json_encode(WantsDatabase::updateWantRequest($body));
        if ($method === 'DELETE') {
            $data = json_decode($body, true);
            echo json_encode(WantsDatabase::deleteWantRequest($data['id']));
        }
        break;

    case 'api/posts/wants/feed':
        $db = new WantsDatabase();
        echo $db->getWantRequestFeed();
        break;

    case 'api/posts/want':
        $wantID = $_GET['id'] ?? '';
        echo WantsDatabase::getWantRequestDetails($wantID);
        break;

    // User
    case 'api/user/profile':
        if ($method === 'GET') echo UserProfile::getUserProfile($body);
        if ($method === 'PUT') echo json_encode(UserProfile::updateProfile($body));
        break;

    case 'api/user/listings':
        $userID = $_GET['user_id'] ?? '';
        echo UserListings::getUserListings($userID);
        break;

    case 'api/user/wants':
        $userID = $_GET['user_id'] ?? '';
        echo WantRequest::getUserWantRequests($userID);
        break;

    // Image upload
    case 'api/upload/image':
        // Handled directly by upload_image.php logic — just include it
        include __DIR__ . '/api/upload/upload_image.php';
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}