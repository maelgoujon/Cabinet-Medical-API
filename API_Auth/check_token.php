<?php

require_once 'jwt_utils.php'; // Include the JWT utility functions


$config = require_once 'config.php'; // Load the configuration

$secret = $config['jwt_secret']; // Get the secret from the configuration

$headers = apache_request_headers(); // Get the request headers
header('Content-Type: application/json');
if (isset($headers['Authorization'])) { // Check if the Authorization header is set
    $authorizationHeader = $headers['Authorization']; // Get the Authorization header
    $headerValue = explode(' ', $authorizationHeader); // Split the header value

    $token = $headerValue[1]; // Get the token from the header value
    try {

        $isValid = is_jwt_valid($token, $secret); // Check if the token is valid
        if ($isValid) {
            echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Token valide"));
        } else {
            echo json_encode(array("status" => "error", "status_code" => 401, "status_message" => "Token invalide ou expiré"));
        }

    } catch (Exception $e) {
        echo json_encode(array("status" => "error", "status_code" => 500, "status_message" => "Erreur dans le token"));
    }
} else {
    echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Token manquant"));
}