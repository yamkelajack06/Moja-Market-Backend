<?php
header("Content-Type: application/json");

require_once("../db_connection.php");

$user1 = $_GET['user1'] ?? null;
$user2 = $_GET['user2'] ?? null;
$item_id = $_GET['item_id'] ?? null;

if (!$user1 || !$user2) {
    echo json_encode(["status" => "error", "message" => "Missing users"]);
    exit;
}

try {

    if ($item_id) {
        $stmt = $pdo->prepare("
            SELECT m.*, u.username AS sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            WHERE (
                (sender_id = :user1 AND receiver_id = :user2)
                OR (sender_id = :user2 AND receiver_id = :user1)
            )
            AND item_id = :item_id
            ORDER BY timestamp ASC
        ");

        $stmt->execute([
            ":user1" => $user1,
            ":user2" => $user2,
            ":item_id" => $item_id
        ]);

    } else {
        $stmt = $pdo->prepare("
            SELECT m.*, u.username AS sender_name
            FROM messages m
            JOIN users u ON m.sender_id = u.user_id
            WHERE (
                (sender_id = :user1 AND receiver_id = :user2)
                OR (sender_id = :user2 AND receiver_id = :user1)
            )
            ORDER BY timestamp ASC
        ");

        $stmt->execute([
            ":user1" => $user1,
            ":user2" => $user2
        ]);
    }

    $messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "messages" => $messages]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
