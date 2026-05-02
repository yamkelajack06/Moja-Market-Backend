<?php
    require_once __DIR__  . '/../../utils/utils.php';
    require_once __DIR__ . '/../../response/response.php';

    class Login {
        public static function login(string $loginID, string $password) {
            try {
                $user = Utility::getUser($loginID, $password);
                
                if ($user == "") {
                    return new Response(false, "Invalid login credentials");
                } 
            } catch (Exception $e) {
                $message = $e -> getMessage();
                return new Response($success,$message);  

            }

            return new Response(true, "Login successful", json_decode($user, true));
        }
    }