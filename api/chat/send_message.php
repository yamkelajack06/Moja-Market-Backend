<?php
header("Content-Type: application/json");

require_once("../db_connection.php"); //Our PDO connection/easily changable

$data = json_decode(file_get_contents("php://input"), true);

$sender_id = $data['sender_id'] ?? null;
$receiver_id = $data['receiver_id'] ?? null;
$message_text = trim($data['message_text'] ?? "");
$item_id = $data['item_id'] ?? null;

if (!$sender_id || !$receiver_id || empty($message_text)) {
    echo json_encode(["status" => "error", "message" => "Missing required fields"]);
    exit;
}

// To prevent self messaging
if ($sender_id == $receiver_id) {
    echo json_encode(["status" => "error", "message" => "Cannot send message to yourself"]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO messages (sender_id, receiver_id, item_id, message_text, timestamp)
        VALUES (:sender_id, :receiver_id, :item_id, :message_text, NOW())
    ");

    $stmt->execute([
        ":sender_id" => $sender_id,
        ":receiver_id" => $receiver_id,
        ":item_id" => $item_id,
        ":message_text" => $message_text
    ]);

    echo json_encode(["status" => "success", "message" => "Message sent"]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
