<?php
include 'check_remote_jwt.php';
include '../Base/config.php';
try {
    $pdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Requête pour récupérer la répartition des usagers selon leur sexe et leur âge
$repartitionUsagers = "SELECT 
CASE WHEN Civilite = 'Mr.' THEN 'Homme'
    WHEN Civilite = 'Mme.' THEN 'Femme'
    ELSE 'Autre' END AS Sexe,
SUM(CASE WHEN YEAR(CURDATE()) - YEAR(STR_TO_DATE(Date_de_naissance, '%d/%m/%Y')) < 25 THEN 1 ELSE 0 END) AS MoinsDe25,
SUM(CASE WHEN YEAR(CURDATE()) - YEAR(STR_TO_DATE(Date_de_naissance, '%d/%m/%Y')) BETWEEN 25 AND 50 THEN 1 ELSE 0 END) AS Entre25Et50,
SUM(CASE WHEN YEAR(CURDATE()) - YEAR(STR_TO_DATE(Date_de_naissance, '%d/%m/%Y')) > 50 THEN 1 ELSE 0 END) AS PlusDe50
FROM patient
GROUP BY Civilite";

function check_jwt_ok()
{
    // Vérifier le token JWT dans Authorization avec la fonction check_remote_jwt
    $headers = apache_request_headers(); // Get the request headers
    header('Content-Type: application/json');
    if (isset($headers['Authorization'])) { // Check if the Authorization header is set
        $authorizationHeader = $headers['Authorization']; // Get the Authorization header
        $headerValue = explode(' ', $authorizationHeader); // Split the header value

        $token = $headerValue[1]; // Get the token from the header value

        $response = check_remote_jwt($token);
        return $response;
    }
}

/******************* GET *******************/
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Vérifier le token JWT
    if (check_jwt_ok()) {
        $getRepartitionUsagersQuery = $pdo->prepare($repartitionUsagers);
        $getRepartitionUsagersQuery->execute();
        $repartitionUsagers = $getRepartitionUsagersQuery->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($repartitionUsagers);
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Accès refusé"));
    }
} else {
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}