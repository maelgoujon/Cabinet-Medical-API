<?php

require_once 'jwt_utils.php'; // Include the JWT utility functions

function check_token()
{
    $config = require_once 'config.php'; // Load the configuration

    $secret = $config['jwt_secret']; // Get the secret from the configuration

    header('Content-Type: application/json');
    if (isset($_GET['token'])) { // Check if the token is set in the URL
        $encodedToken = $_GET['token']; // Get the encoded token from the URL
        $token = urldecode($encodedToken); // Decode the token
        try {
            $isValid = is_jwt_valid($token, $secret); // Check if the token is valid
            if ($isValid) {
                http_response_code(200);
                echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Token valide"));
            } else {
                http_response_code(401);
                echo json_encode(array("status" => "error", "status_code" => 401, "status_message" => "Token invalide ou expiré", "token" => $token));
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(array("status" => "error", "status_code" => 500, "status_message" => "Erreur dans le token"));
        }
    } else {
        http_response_code(400);
        echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Token manquant"));
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    check_token();
}