<?php
   require_once __DIR__ . '/config/db.php';
   require_once __DIR__ . '/api/auth/login.php';
   require_once __DIR__ . '/api/auth/register.php';

   function readInput() {
    $name     = readline("Enter name: ");
    $surname  = readline("Enter surname: ");
    $username = readline("Enter username: ");
    $email    = readline("Enter email: ");
    $password = readline("Enter password: ");
    $userID   = uniqid('user_', true);

    return json_encode([
        'userID'   => $userID,
        'name'     => $name,
        'surname'  => $surname,
        'username' => $username,
        'email'    => $email,
        'password' => $password
    ]);
}

$json = json_encode([
    'userID'   => 'user_' . bin2hex(random_bytes(16)),
    'name'     => 'Yamkela',
    'surname'  => 'Jack',
    'username' => 'jack1011',
    'email'    => 'yamkelajack101@gmail.com',
    'password' => 'jack101@123'
]);


$user = json_decode($json,true);

$email = $user['email'];
$username = $user['username'];
$password = $user['password'];

$login = Login::login($email,$password);

echo $login -> getMessage();

   