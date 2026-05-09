<?php

header('Content-Type: application/json');

require_once 'messages.php';

try {

    if(!isset($_GET['chatID'])) {

        echo json_encode([
            'success' => false,
            'message' => 'chatID missing'
        ]);

        exit;
    }

    $chatID = $_GET['chatID'];

    $response = Messages::getChatHistory($chatID);

    echo json_encode($response);

} catch(Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}