<?php

require_once 'jwt_utils.php'; // Include the JWT utility functions

$config = require_once 'config.php'; // Load the configuration

$secret = $config['jwt_secret']; // Get the secret from the configuration

function check_token() {
    $headers = apache_request_headers(); // Get the request headers

    if (isset($headers['Authorization'])) { // Check if the Authorization header is set
        $authorizationHeader = $headers['Authorization']; // Get the Authorization header
        $headerValue = explode(' ', $authorizationHeader); // Split the header value

        $token = $headerValue[1]; // Get the token from the header value
        try {
            $isValid = is_jwt_valid($token, $GLOBALS['secret']); // Check if the token is valid
            return $isValid;
        } catch (Exception $e) {
            return false;
        }
    } else {
        return false;
    }
}