<?php
// home file in charge of redirections in function of the method

//session_start(); // Start the session

// if user is asking for GET method, check the validity of the token with check_token function from check_token.php
include_once 'check_token.php';
include_once 'getjwt.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    header('Content-Type: application/json');
    //$jwt = $_SESSION["token"];
    //echo json_encode(array("data" => $jwt));
    $authorizationHeader = $headers['Authorization']; // Get the Authorization header
    $headerValue = explode(' ', $authorizationHeader); // Split the header value
    $token = $headerValue[1]; // Get the token from the header value
    $jwt = $token;
    check_token($jwt);
}



// if user is asking for POST method, check the user and password and send the token with get_jwt($login, $password) function from getjwt.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // si l'utilisateur est deja authentifié on supprime le JWT
    if (isset($headers['Authorization'])) {
        unset($headers['Authorization']);
    }

    $jwt = get_jwt($data['login'], $data['mdp']);


    header('Content-Type: application/json');
    if ($jwt !== 'false') {
        $headers['Authorization'] = $jwt;
        echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Authentification OK", "data" => $jwt));
    } else {
        echo json_encode(array("status" => "error", "status_code" => 401, "status_message" => "Login ou mot de passe incorrect"));
    }

}