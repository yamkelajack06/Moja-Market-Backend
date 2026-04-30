<?php
    class AuthResponse {
        private bool $success;
        private string $message;

        function __construct(bool $success, string $message) {
            $this -> success = $success;
            $this -> message = $message;
        }

        public function getSuccess() {
            return $success;
        }

        public function getMessage() {
            return $message;
        }
    }