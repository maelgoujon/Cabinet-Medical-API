<?php

require 'jwt_utils.php';

function get_jwt($login, $password)
{
    $config = require 'config.php';

    if (!is_array($config) || !isset($config['jwt_secret'])) {
        return "Invalid configuration";
    }

    $secret = $config['jwt_secret'];

    $record = [
        "login" => "secretaire1",
        "password" => "password1234!"
    ];

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!is_array($data) || !isset($data['login']) || !isset($data['mdp'])) {
            return "Invalid data";
        }

        $login = $data['login'];
        $password = $data['mdp'];

        if ($login == $record['login'] && $password == $record['password']) {
            $payload = array(
                "login" => $login,
                "password" => $password,
                "exp" => time() + 3600
            );

            $headers = apache_request_headers();

            $jwt = generate_jwt($headers, $payload, $secret);
            return $jwt;
        } else {
            return "Invalid credentials";
        }
    } else {
        return "Invalid request method";
    }
}