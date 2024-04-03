<?php

require_once 'jwt_utils.php'; // Include the JWT utility functions

$config = require_once 'config.php'; // Load the configuration

$secret = $config['jwt_secret']; // Get the secret from the configuration

function check_token($jwt) {
    $headers = apache_request_headers(); // Get the request headers

    // Check if the Authorization header is set
    if (isset($headers['Authorization'])) {
        $authorizationHeader = $headers['Authorization']; // Get the Authorization header
        $headerValue = explode(' ', $authorizationHeader); // Split the header value

        $token = $headerValue[1]; // Get the token from the header value
        try {
            $isValid = is_jwt_valid($token, $GLOBALS['secret']); // Check if the token is valid
            if (!$isValid) {
                echo json_encode(array("status" => "error", "status_code" => 401, "status_message" => "Token is invalid"));

            } else {
                echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Token is valid", "data" => $token));
            }
        } catch (Exception $e) {
            echo json_encode(array("status" => "error", "status_code" => 401, "status_message" => "Token is invalid"));
            
        }
    } else {
        echo json_encode(array("status" => "error", "status_code" => 401, "status_message" => "Token is missing"));

    }
}

