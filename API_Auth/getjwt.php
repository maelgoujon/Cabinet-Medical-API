<?php

require 'jwt_utils.php';

function get_jwt($login, $password)
{
    // return the value of $tab in API_Auth/config.php
    $config = require '../API_Auth/config.php';
    if (!is_array($config) || !isset($config['jwt_secret'])) {
        return "Invalid config";
    }


    $secret = $config['jwt_secret'];

    $record = [
        "login" => "secretaire1",
        "password" => "password1234!"
    ];


    // si l'utilisateur est deja authentifié
    if (isset($_SESSION["jwt"])) {
        return $_SESSION["jwt"];
    }



    if ($login == $record['login'] && $password == $record['password']) {
        $payload = array(
            "login" => $login,
            "password" => $password,
            "exp" => time() + 3600
        );

        $headers = array(
            "alg" => "HS256",
            "typ" => "JWT"
        );

        $jwt = generate_jwt($headers, $payload, $secret);
        return $jwt;
    } else {
        return false;
    }

}