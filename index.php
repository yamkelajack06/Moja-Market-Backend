<?php
ini_set('display_errors', 1); 
error_reporting(E_ALL);

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
// require_once __DIR__ . '/utils/image_upload.php';

header('Content-Type: application/json');
header("Access-Control-Allow-Origin: https://moja-market-web.vercel.app");
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$request_uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$body = file_get_contents('php://input');

if (preg_match('/api\/[a-zA-Z0-9\/_-]+/', $request_uri, $matches)) {
    $path = rtrim($matches[0], '/');
} else {
    $path = trim($request_uri, '/');
}

switch ($path) {
    case 'api/upload':
        if ($method === 'POST') {
            $res = ImageUpload::uploadImage();
            echo json_encode($res->toArray());
        } else {
            http_response_code(405);
            echo json_encode(['success' => false, 'message' => 'Method not allowed']);
        }
        break;

    case 'api/auth/register':
        $res = Register::registerUser($body);
        echo json_encode($res->toArray());
        break;

    case 'api/auth/login':
        $data = json_decode($body, true);
        $res = Login::login($data['loginID'] ?? '', $data['password'] ?? '');
        echo json_encode($res->toArray());
        break;

    case 'api/posts/items':
        if ($method === 'POST')   echo json_encode(ItemDatabase::postItem($body)->toArray());
        if ($method === 'PUT')    echo json_encode(ItemDatabase::updateItem($body)->toArray());
        if ($method === 'DELETE') echo json_encode(ItemDatabase::deleteItem($body)->toArray());
        break;

    case 'api/posts/feed':
        echo json_encode(ItemDatabase::getFeed()->toArray());
        break;

    case 'api/posts/item':
        $itemID = $_GET['id'] ?? '';
        echo json_encode(ItemDatabase::getItemDetails($itemID)->toArray());
        break;

    case 'api/posts/wants':
        if ($method === 'POST') echo json_encode(WantsDatabase::postWantRequest($body)->toArray());
        if ($method === 'PUT')  echo json_encode(WantsDatabase::updateWantRequest($body)->toArray());
        if ($method === 'DELETE') {
            $data = json_decode($body, true);
            echo json_encode(WantsDatabase::deleteWantRequest($data['id'] ?? '')->toArray());
        }
        break;

    case 'api/posts/wants/feed':
        $db = new WantsDatabase();
        echo json_encode($db->getWantRequestFeed()->toArray());
        break;

    case 'api/posts/want':
        $wantID = $_GET['id'] ?? '';
        echo json_encode(WantsDatabase::getWantRequestDetails($wantID)->toArray());
        break;

    case 'api/user/profile':
        if ($method === 'GET') echo json_encode(UserProfile::getUserProfile($body)->toArray());
        if ($method === 'PUT') echo json_encode(UserProfile::updateProfile($body)->toArray());
        break;

    case 'api/user/listings':
        $userID = $_GET['user_id'] ?? '';
        echo json_encode(UserListings::getUserListings($userID)->toArray());
        break;

    case 'api/user/wants':
        $userID = $_GET['user_id'] ?? '';
        echo json_encode(WantRequest::getUserWantRequests($userID)->toArray());
        break;

    default:
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Endpoint not found']);
        break;
}