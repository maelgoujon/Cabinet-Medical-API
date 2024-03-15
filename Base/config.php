<?php
$server = '172.17.0.1:3306';
$db = 'PHP_Project';
$login = "etu";
$mdp = "coucou";

// Connexion à la base de données
$conn = new mysqli($server, $login, $mdp, $db);

// Vérification de la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

?>
