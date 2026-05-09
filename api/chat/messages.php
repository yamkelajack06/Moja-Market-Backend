<?php
    require_once __DIR__ . '/../../config/db.php';
    require_once __DIR__ . '/../../response/response.php';
    require_once __DIR__ . '/../../utils/utils.php';

    class Messages {

        // Creates a new chat linked to an item or want.
        // If a chat already exists for the same users + item/want, returns the existing one.
        public static function createChat(string $json) {
            $data   = json_decode($json, true);
            $chatID = $data['chatID'];
            $user1  = $data['user1'];
            $user2  = $data['user2'];
            $itemID = $data['itemID'] ?? null;
            $wantID = $data['wantID'] ?? null;

            if (!empty($itemID)) {
                $existingChatID = Utility::getChatByItem($user1, $user2, $itemID);
            } else if (!empty($wantID)) {
                $existingChatID = Utility::getChatByWant($user1, $user2, $wantID);
            } else {
                return new Response(false, 'Either itemID or wantID is required');
            }

            if ($existingChatID) {
                return new Response(true, 'Chat already exists', ['chatID' => $existingChatID]);
            }

            $itemVal = $itemID ? "'" . $itemID . "'" : "NULL";
            $wantVal = $wantID ? "'" . $wantID . "'" : "NULL";

            $query = "INSERT INTO Chat (chat_id, user_1, user_2, item_id, want_id) VALUES ('" . $chatID . "', '" . $user1 . "', '" . $user2 . "', " . $itemVal . ", " . $wantVal . ")";

            $result = Database::queryDatabase($query);

            if (!$result) {
                return new Response(false, 'Failed to create chat');
            }

            return new Response(true, 'Chat created successfully', ['chatID' => $chatID]);
        }

        // Sends a message in a chat
        public static function sendMessage(string $json) {
            $conn      = Database::connect();
            $data      = json_decode($json, true);
            $messageID = $data['messageID'];
            $chatID    = $data['chatID'];
            $senderID  = $data['senderID'];
            $content   = pg_escape_string($conn, $data['message']);

            $query = "INSERT INTO Message (message_id, chat_id, message_sender, message_content) VALUES ('" . $messageID . "', '" . $chatID . "', '" . $senderID . "', '" . $content . "')";

            $result = Database::queryDatabase($query);

            if (!$result) {
                return new Response(false, 'Failed to send message');
            }

            return new Response(true, 'Message sent successfully');
        }

        // Returns full message history for a chat, oldest first.
        // Called once when the chat screen opens.
        public static function getChatHistory(string $chatID) {
            $query = "SELECT message_id, message_sender, message_content, to_char(time_sent, 'YYYY-MM-DD HH24:MI:SS') AS time_sent FROM Message WHERE chat_id = '" . $chatID . "' ORDER BY time_sent ASC";

            $result = Database::queryDatabase($query);

            $messages = [];
            while ($row = pg_fetch_assoc($result)) {
                $messages[] = $row;
            }

            return new Response(true, 'Chat history retrieved', $messages);
        }

        // Returns only messages newer than lastTime.
        // Short polling endpoint — called every 3 seconds from the frontend.
        public static function getNewMessages(string $chatID, string $lastTime) {
            $query = "SELECT message_id, message_sender, message_content, to_char(time_sent, 'YYYY-MM-DD HH24:MI:SS') AS time_sent FROM Message WHERE chat_id = '" . $chatID . "' AND time_sent > '" . $lastTime . "'::timestamp ORDER BY time_sent ASC";

            $result = Database::queryDatabase($query);

            $messages = [];
            while ($row = pg_fetch_assoc($result)) {
                $messages[] = $row;
            }

            return new Response(true, 'Messages retrieved', $messages);
        }

        // Returns all chats for a user with the other person's details and last message.
        // Used to populate the chats list screen.
        public static function getUserChats(string $userID) {
            $query = "
                SELECT
                    c.chat_id,
                    c.item_id,
                    c.want_id,
                    CASE WHEN c.user_1 = '" . $userID . "' THEN c.user_2 ELSE c.user_1 END AS other_user_id,
                    u.name,
                    u.username,
                    last_msg.message_content AS last_message,
                    to_char(last_msg.time_sent, 'YYYY-MM-DD HH24:MI:SS') AS last_message_time
                FROM Chat c
                JOIN Users u
                    ON u.user_id = CASE WHEN c.user_1 = '" . $userID . "' THEN c.user_2 ELSE c.user_1 END
                LEFT JOIN LATERAL (
                    SELECT message_content, time_sent
                    FROM Message
                    WHERE chat_id = c.chat_id
                    ORDER BY time_sent DESC
                    LIMIT 1
                ) last_msg ON true
                WHERE c.user_1 = '" . $userID . "' OR c.user_2 = '" . $userID . "'
                ORDER BY last_msg.time_sent DESC NULLS LAST
            ";

            $result = Database::queryDatabase($query);

            $chats = [];
            while ($row = pg_fetch_assoc($result)) {
                $chats[] = $row;
            }

            return new Response(true, 'Chats retrieved', $chats);
        }
    }