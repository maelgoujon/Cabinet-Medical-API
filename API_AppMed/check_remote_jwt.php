<?php
function check_remote_jwt($token)
{
    $url = 'http://172.17.0.1:5050/API_Auth/login.php';

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $token
    ));
    $result = curl_exec($ch);

    if (curl_error($ch)) {
        curl_close($ch);
        return False;
    } else {
        curl_close($ch);
        $response = json_decode($result, true);
        if (isset($response['status']) && $response['status'] == 'success') {
            return True;
        } else {
            
            return False;
        }
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
            http_response_code(200);
            
        } else {
            http_response_code(401);
        }
    } else {
        http_response_code(400);
    }
}

// if user is asking for POST method, check the user and password and send the token
*/