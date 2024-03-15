<?php
include '../Base/config.php';   // Inclusion de la connexion à la base de données
header('Content-Type: application/json');

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    $linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'GET') {


        $sql = "SELECT * FROM patient";

        $stmt = $linkpdo->query($sql);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode($row);


    }


} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>