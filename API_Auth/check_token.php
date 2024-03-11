<?php

require_once 'jwt_utils.php'; // Include the JWT utility functions


$config = require_once 'config.php'; // Load the configuration

$secret = $config['jwt_secret']; // Get the secret from the configuration

$headers = apache_request_headers(); // Get the request headers

if (isset($headers['Authorization'])) { // Check if the Authorization header is set
    $authorizationHeader = $headers['Authorization']; // Get the Authorization header
    $headerValue = explode(' ', $authorizationHeader); // Split the header value

    $token = $headerValue[1]; // Get the token from the header value
    try {
        
        $decoded2 = is_jwt_valid($token, $secret);

        $isValid = is_jwt_valid($token, $secret); // Check if the token is valid
        if ($isValid) {
            echo 'Token is valid'; // Output a success message
        } else {
            echo 'Token is invalid'; // Output an error message
        }

    } catch (Exception $e) {
        echo 'Invalid token: ' . $e->getMessage(); // Output the error message
    }
} else {
    echo 'Authorization header is missing'; // Output an error message
}