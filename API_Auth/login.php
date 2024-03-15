<?php
require_once '../API_Auth/getjwt.php';
// Démarrer la session
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents('php://input'), true);

    // si l'utilisateur est deja authentifié on supprime le JWT
    if (isset($_SESSION["jwt"])) {
        unset($_SESSION["jwt"]);
    }

    $jwt = get_jwt($data['login'], $data['mdp']);
    
    header('Content-Type: application/json');
    if ($jwt) {
        $_SESSION["jwt"] = $jwt;
        echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Authentification OK", "data" => $jwt));
    } else {
        echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Login ou mot de passe incorrect"));
    }
} else {
    echo json_encode("Invalid request method");
}