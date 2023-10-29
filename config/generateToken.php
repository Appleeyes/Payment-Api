<?php

use Firebase\JWT\JWT;

// Include your configuration and secret key
require_once __DIR__ . '/container.php';

// Generate a token for the API client
$jwtPayload = [];

$jwtSecretKey = $_ENV['JWT_SECRET_KEY'];
$token = JWT::encode($jwtPayload, (string)$jwtSecretKey, 'HS256');

// Return the token as JSON response
$response = [
    'token' => $token,
];

header('Content-Type: application/json');
echo json_encode($response);