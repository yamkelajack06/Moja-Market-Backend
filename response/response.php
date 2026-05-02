<?php
    class Response {
        private bool $success;
        private string $message;
        private array $data;

        function __construct(bool $success, string $message) {
            $this -> success = $success;
            $this -> message = $message;
        }

        public function getSuccess() {
            return $this -> success;
        }

        public function getMessage() {
            return $this -> message;
        }
    }