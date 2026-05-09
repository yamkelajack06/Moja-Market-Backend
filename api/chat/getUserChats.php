<?php

header('Content-Type: application/json');

require_once 'messages.php';

try {

    if(!isset($_GET['userID'])) {

        echo json_encode([
            'success' => false,
            'message' => 'userID missing'
        ]);

        exit;
    }

    $userID = $_GET['userID'];

    $response = Messages::getUserChats($userID);

    echo json_encode($response);

} catch(Exception $e) {

    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}