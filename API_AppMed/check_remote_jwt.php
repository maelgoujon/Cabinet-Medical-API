<?php

function check_remote_jwt($token)
{
    $encodedToken = urlencode($token); // Encode the token

    $url = 'http://172.17.0.1:5050/API_Auth/check_token.php?token=' . $encodedToken;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $result = curl_exec($ch);

    if (curl_error($ch)) {
        http_response_code(401);
        curl_close($ch);
        return False;
    } else {
        http_response_code(200);
        curl_close($ch);
        return True;
    }
}

/*

// if user is asking for GET method, get the token from the Authorization header
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Get the request headers
    $headers = apache_request_headers();
    header('Content-Type: application/json');
    if (isset($headers['Authorization'])) { // Check if the Authorization header is set
        $authorizationHeader = $headers['Authorization']; // Get the Authorization header
        
        $headerValue = explode(' ', $authorizationHeader); // Split the header value

        $token = $headerValue[1]; // Get the token from the header value

        $response = check_remote_jwt($token);

        if ($response) {
            echo json_encode(['message' => 'Token valide from check_remote_jwt']);
        } else {
            http_response_code(401);
            echo json_encode(['message' => 'Token invalide from check_remote_jwt', 'Response' => $response, 'Token' => $token]);
        }
    }
} else {
    http_response_code(401);
    echo json_encode(['message' => 'Token manquant from check_remote_jwt']);
}

*/