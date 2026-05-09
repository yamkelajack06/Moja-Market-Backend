<?php

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

function route($method, $uri) {
    $path = parse_url($uri, PHP_URL_PATH);

    header('Content-Type: application/json');

    switch ($path) {

        case '/api/auth/register':
            $json = file_get_contents('php://input');
            echo json_encode(Register::registerUser($json)->toArray());
            break;

        case '/api/auth/login':
            $body     = json_decode(file_get_contents('php://input'), true);
            $loginID  = $body['loginID']  ?? '';
            $password = $body['password'] ?? '';
            echo json_encode(Login::login($loginID, $password)->toArray());
            break;

        case '/api/posts/items':
            $json = file_get_contents('php://input');
            echo json_encode(ItemDatabase::postItem($json)->toArray());
            break;

        case '/api/posts/feed':
            echo json_encode(ItemDatabase::getFeed()->toArray());
            break;

        case '/api/posts/item':
            $body   = json_decode(file_get_contents('php://input'), true);
            $itemID = $body['itemID'] ?? '';
            echo json_encode(ItemDatabase::getItemDetails($itemID)->toArray());
            break;

        case '/api/posts/wants':
            $json = file_get_contents('php://input');
            echo json_encode(WantsDatabase::postWantRequest($json)->toArray());
            break;

        case '/api/posts/wants/feed':
            echo json_encode((new WantsDatabase())->getWantRequestFeed()->toArray());
            break;

        case '/api/posts/want':
            $body   = json_decode(file_get_contents('php://input'), true);
            $wantID = $body['wantsID'] ?? '';
            echo json_encode(WantsDatabase::getWantRequestDetails($wantID)->toArray());
            break;

        case '/api/user/profile':
            $json = file_get_contents('php://input');
            $body = json_decode($json, true);
            if (isset($body['name'])) {
                echo json_encode(UserProfile::updateProfile($json)->toArray());
            } else {
                echo json_encode(UserProfile::getUserProfile($json)->toArray());
            }
            break;

        case '/api/user/listings':
            $body   = json_decode(file_get_contents('php://input'), true);
            $userID = $body['userID'] ?? '';
            echo json_encode(UserListings::getUserListings($userID)->toArray());
            break;

        case '/api/user/wants':
            $body   = json_decode(file_get_contents('php://input'), true);
            $userID = $body['userID'] ?? '';
            echo json_encode(WantRequest::getUserWantRequests($userID)->toArray());
            break;

        case '/api/upload':
            require_once __DIR__ . '/api/upload/upload_image.php';
            echo json_encode(ImageUpload::uploadImage()->toArray());
            break;

        case '/api/chat/create':
            $json = file_get_contents('php://input');
            echo json_encode(Messages::createChat($json)->toArray());
            break;

        case '/api/chat/send':
            $json = file_get_contents('php://input');
            echo json_encode(Messages::sendMessage($json)->toArray());
            break;

        case '/api/chat/history':
            $chatID = $_GET['chatID'] ?? '';
            echo json_encode(Messages::getChatHistory($chatID)->toArray());
            break;

        case '/api/chat/messages':
            $chatID   = $_GET['chatID']   ?? '';
            $lastTime = $_GET['lastTime'] ?? '2000-01-01 00:00:00';
            echo json_encode(Messages::getNewMessages($chatID, $lastTime)->toArray());
            break;

        case '/api/chat/list':
            $body   = json_decode(file_get_contents('php://input'), true);
            $userID = $body['userID'] ?? '';
            echo json_encode(Messages::getUserChats($userID)->toArray());
            break;

        default:
            http_response_code(404);
            echo json_encode(["error" => "404 Not Found", "path" => $path]);
            break;
    }
}