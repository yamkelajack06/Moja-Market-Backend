<?php
header("Content-Type: application/json");

require_once("../db_connection.php");

$user_id = $_GET['user_id'] ?? null;

if (!$user_id) {
    echo json_encode(["status" => "error", "message" => "Missing user_id"]);
    exit;
}

try {

    $stmt = $pdo->prepare("
        SELECT DISTINCT ON (partner_id)
            m.message_id,
            m.message_text,
            m.timestamp,
            partner.user_id AS partner_id,
            partner.username AS partner_name
        FROM (
            SELECT *,
                CASE
                    WHEN sender_id = :user_id THEN receiver_id
                    ELSE sender_id
                END AS partner_id
            FROM messages
            WHERE sender_id = :user_id OR receiver_id = :user_id
        ) m
        JOIN users partner ON partner.user_id = m.partner_id
        ORDER BY partner_id, timestamp DESC
    ");

    $stmt->execute([":user_id" => $user_id]);

    $chats = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(["status" => "success", "chats" => $chats]);

} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>
