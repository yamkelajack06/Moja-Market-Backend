<?php
    require_once __DIR__  . '/../../utils/utils.php';
    require_once __DIR__ . '/../../response/response.php';

    class Login {
        public static function login(string $loginID, string $password) {
            $success = false;
            $message = "";
            try {
                $user = Utility::getUser($loginID, $password);
                
                if ($user == "") {
                    $message = "Invalid login credentials";
                } else {
                    $success = true;
                    $message = "Login successful";
                }

            } catch (Exception $e) {
                $message = $e -> getMessage();
                return new Response($success,$message);  

            }

            return new Response($success,$message);
        }
    }