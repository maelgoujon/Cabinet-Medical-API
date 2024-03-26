<?php

require_once 'jwt_utils.php'; // Include the JWT utility functions

function check_token()
{

    $config = require_once 'config.php'; // Load the configuration

    $secret = $config['jwt_secret']; // Get the secret from the configuration

    $headers = apache_request_headers(); // Get the request headers
    header('Content-Type: application/json');
    if (isset ($headers['Authorization'])) { // Check if the Authorization header is set
        $authorizationHeader = $headers['Authorization']; // Get the Authorization header
        $headerValue = explode(' ', $authorizationHeader); // Split the header value

        $token = $headerValue[1]; // Get the token from the header value
        try {

            $isValid = is_jwt_valid($token, $secret); // Check if the token is valid
            if ($isValid) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Token valide"));
                return true;
            } else {
                http_response_code(401);
                echo json_encode(array("status" => "error", "status_code" => 401, "status_message" => "Token invalide ou expiré"));
                return false;
            }

        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("status" => "error", "status_code" => 500, "status_message" => "Erreur dans le token"));
            return false;
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Token manquant"));
        return false;
    }
}