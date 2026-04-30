<?php
    class User {
        private $name;
        private $surname;
        private $username;
        private $email;
        private $password;
        private $userID;

        function __construct($userID,$name,$surname,$username,$email,$password) {
            $this -> $userID = $userID;
            $this -> $name = $name;
            $this -> $surname = $surname;
            $this -> $username = $username;
            $this -> $email = $email;
            $this -> $password = $password;
        }
    }