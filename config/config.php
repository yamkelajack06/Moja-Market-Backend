<?php
// Get the environment variables from the .env file
$env = parse_ini_file(__DIR__ . '/../.env');

//Define the constants for the database connection
define('DB_HOST', $env['DB_HOST']);
define('DB_NAME', $env['DB_NAME']);
define('DB_USER', $env['DB_USER']);
define('DB_PASS', $env['DB_PASS']);
define('DB_PORT', $env['DB_PORT']);