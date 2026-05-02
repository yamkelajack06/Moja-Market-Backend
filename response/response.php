<?php
    class Response {
        private bool $success;
        private string $message;
        private mixed $data;

        function __construct(bool $success, string $message, mixed $data = null) {
            $this -> success = $success;
            $this -> message = $message;
            $this->data    = $data;
        }

        public function getSuccess() {
            return $this -> success;
        }

        public function getMessage() {
            return $this -> message;
        }

        public function getData() { 
            return $this->data; 
        }

        public function toArray() {
            return [
                'success' => $this->success,
                'message' => $this->message,
                'data'    => $this->data
            ];
        }
    }