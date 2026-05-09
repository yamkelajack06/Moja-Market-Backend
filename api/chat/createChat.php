<?php

header('Content-Type: application/json');

require_once 'messages.php';

try {

    $json = file_get_contents("php://input");

    $response = Messages::createChat($json);

    echo json_encode($response);

} catch(Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}