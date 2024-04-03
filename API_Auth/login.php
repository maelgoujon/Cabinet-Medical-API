<?php
require_once 'check_token.php'; // Include the check_token function
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    $authorizationHeader = $headers['Authorization']; // Get the Authorization header
    $headerValue = explode(' ', $authorizationHeader); // Split the header value
    $token = $headerValue[1]; // Get the token from the header value
    check_token($token);
}