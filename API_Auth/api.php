<?php
require 'jwt_utils.php';

$secret = "your-secret-key"; // Remplacez par votre propre clé secrète

function getData($request)
{
    $data = array();
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $data['message'] = "Data for id: " . $id;
    } else {
        $data['error'] = true;
        $data['message'] = "No ID provided";
    }
    return $data;
}

function sendResponse($response)
{
    header('Content-Type: application/json');
    echo json_encode($response);
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $request = $_GET;
    $token = get_bearer_token();

    if (is_jwt_valid($token, $secret)) {
        $data = getData($request);
        sendResponse($data);
    } else {
        $response['error'] = true;
        $response['message'] = "Invalid JWT";
        $response['token'] = $token;
        sendResponse($response);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //generate a new JWT from the credentials provided
    $data = json_decode(file_get_contents('php://input'), true);
    $login = $data['login'];
    $mdp = $data['mdp'];

    if ($login == "secretaire1" && $mdp == "password1234!") {
        $headers = array("alg" => "HS256", "typ" => "JWT");
        $payload = array("sub" => "secretaire1", "exp" => time() + 3600);
        $jwt = generate_jwt($headers, $payload, $secret);
        $response['error'] = false;
        $response['token'] = $jwt;
        $response['isJWTValid'] = is_jwt_valid($jwt, $secret);
        sendResponse($response);
    } else {
        $response['error'] = true;
        $response['message'] = "Invalid credentials";
        sendResponse($response);
    }

} else {
    $response['error'] = true;
    $response['message'] = "Invalid request method";
    sendResponse($response);
}
?>