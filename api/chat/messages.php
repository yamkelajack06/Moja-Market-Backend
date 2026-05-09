<?php

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../response/response.php';

class Messages {

    // CREATE CHAT
    
    public static function createChat(string $json) {

        $conn = Database::connect();

        try {

            $data = json_decode($json, true);

            $chatID = $data['chatID'];
            $user1 = $data['user1'];
            $user2 = $data['user2'];
            $itemID = $data['itemID'] ?? null;
            $wantID = $data['wantID'] ?? null;

            // Check if chat already exists
            $checkQuery = "
            SELECT chat_id
            FROM Chat
            WHERE
            (user_1 = $1 AND user_2 = $2)
            OR
            (user_1 = $2 AND user_2 = $1)
            LIMIT 1
            ";

            $checkResult = pg_query_params(
                $conn,
                $checkQuery,
                [$user1, $user2]
            );

            if(pg_num_rows($checkResult) > 0){

                $existing = pg_fetch_assoc($checkResult);

                return new Response(
                    true,
                    'Chat already exists',
                    ['chatID' => $existing['chat_id']]
                );
            }

            $insertQuery = "
            INSERT INTO Chat(
                chat_id,
                user_1,
                user_2,
                item_id,
                want_id
            )
            VALUES($1, $2, $3, $4, $5)
            ";

            $result = pg_query_params(
                $conn,
                $insertQuery,
                [$chatID, $user1, $user2, $itemID, $wantID]
            );

            if(!$result){
                return new Response(false, 'Failed to create chat');
            }

            return new Response(
                true,
                'Chat created successfully',
                ['chatID' => $chatID]
            );

        } catch(Exception $e){
            return new Response(false, $e->getMessage());
        }
    }

    // SEND MESSAGE

    public static function sendMessage(string $json) {

        $conn = Database::connect();

        try {

            $data = json_decode($json, true);

            $messageID = $data['messageID'];
            $chatID = $data['chatID'];
            $sender = $data['senderID'];
            $content = $data['message'];

            $query = "
            INSERT INTO Message(
                message_id,
                chat_id,
                message_sender,
                message_content
            )
            VALUES($1, $2, $3, $4)
            ";

            $result = pg_query_params(
                $conn,
                $query,
                [$messageID, $chatID, $sender, $content]
            );

            if(!$result){
                return new Response(false, 'Failed to send message');
            }

            return new Response(true, 'Message sent successfully');

        } catch(Exception $e){
            return new Response(false, $e->getMessage());
        }
    }

    // GET CHAT HISTORY

    public static function getChatHistory(string $chatID) {

        $conn = Database::connect();

        try {

            $query = "
            SELECT *
            FROM Message
            WHERE chat_id = $1
            ORDER BY time_sent ASC
            ";

            $result = pg_query_params(
                $conn,
                $query,
                [$chatID]
            );

            $messages = [];

            while($row = pg_fetch_assoc($result)){
                $messages[] = $row;
            }

            return new Response(
                true,
                'Chat history retrieved',
                $messages
            );

        } catch(Exception $e){
            return new Response(false, $e->getMessage());
        }
    }

    /* SHORT POLLING
     GETS NEW MESSAGES ONLY, SWEET STUFF */
    
    public static function getMessages(
        string $chatID,
        string $lastTime
    ) {

        $conn = Database::connect();

        try {

            $query = "
            SELECT *
            FROM Message
            WHERE chat_id = $1
            AND time_sent > $2
            ORDER BY time_sent ASC
            ";

            $result = pg_query_params(
                $conn,
                $query,
                [$chatID, $lastTime]
            );

            $messages = [];

            while($row = pg_fetch_assoc($result)){
                $messages[] = $row;
            }

            return new Response(
                true,
                'Messages retrieved',
                $messages
            );

        } catch(Exception $e){
            return new Response(false, $e->getMessage());
        }
    }

    // GET USER CHATS

    public static function getUserChats(string $userID) {

        $conn = Database::connect();

        try {

            $query = "
            SELECT
                c.chat_id,
                c.user_1,
                c.user_2,
                m.message_content,
                m.time_sent
            FROM Chat c
            LEFT JOIN Message m
            ON c.chat_id = m.chat_id
            WHERE
            c.user_1 = $1
            OR
            c.user_2 = $1
            ORDER BY m.time_sent DESC
            ";

            $result = pg_query_params(
                $conn,
                $query,
                [$userID]
            );

            $chats = [];

            while($row = pg_fetch_assoc($result)){
                $chats[] = $row;
            }

            return new Response(
                true,
                'Chats retrieved successfully',
                $chats
            );

        } catch(Exception $e){
            return new Response(false, $e->getMessage());
        }
    }
}