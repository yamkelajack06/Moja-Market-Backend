<?php
function route($method, $uri) {
    $path = parse_url($uri, PHP_URL_PATH);

    switch ($path) {
        case '/api/auth/register':
            require 'auth/register.php';
            break;
        case '/api/auth/login':
            require 'auth/login.php';
            break;
        case '/api/posts/items':
            require 'posts/items.php';
            break;
        case '/api/posts/feed':
            require 'posts/feed.php';
            break;
        case '/api/posts/item':
            require 'posts/item_detail.php';
            break;
        case '/api/posts/wants':
            require 'posts/wants_post.php';
            break;
        case '/api/posts/wants/feed':
            require 'posts/wants_feed.php';
            break;
        case '/api/posts/want':
            require 'posts/want_detail.php';
            break;
        case '/api/user/profile':
            require 'user/profile.php';
            break;
        case '/api/user/listings':
            require 'user/listings.php';
            break;
        case '/api/user/wants':
            require 'user/wants.php';
            break;
        case '/api/upload':
            require 'upload.php';
            break;
        default:
            http_response_code(404);
            header('Content-Type: application/json');
            echo json_encode(["error" => "404 Not Found", "path" => $path]);
            break;
    }
}