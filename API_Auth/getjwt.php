<?php

require_once 'jwt_utils.php';


$config = require_once 'config.php';

$secret = $config['jwt_secret'];

$record = [
    "login" => "secretaire1",
    "password" => "password1234!"
];

//check if the user input match the records
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);
    $login = $data['login'];
    $password = $data['mdp'];

    if ($login == $record['login'] && $password == $record['password']) {
        $payload = array(
            "login" => $login,
            "password"=> $password,
            "exp" => time() + 3600
        );

        //get header
        $headers = apache_request_headers();

        $jwt = generate_jwt($headers, $payload, $secret);
        $response['error'] = false;
        $response['token'] = $jwt;
        $response['exp'] = $payload['exp'];
        echo json_encode($response);
    } else {
        $response['error'] = true;
        $response['message'] = "Invalid credentials";
        echo json_encode($response);
    }
} else {
    $response['error'] = true;
    $response['message'] = "Invalid request method";
    echo json_encode($response);
}
