<?php
include '../Base/config.php';
include 'check_remote_jwt.php';
try {
    $pdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Requête pour récupérer la durée totale des consultations par médecin
$dureeMedecin = "SELECT m.idMedecin, 
CASE WHEN m.Civilite = 'Mr.' THEN 'Monsieur' 
        WHEN m.Civilite = 'Mme.' THEN 'Madame' 
        ELSE 'Autre' END AS Civilitemedecin,
m.Prenom, m.Nom,
SEC_TO_TIME(SUM(c.Duree * 60)) AS DureeTotale
FROM medecin m
LEFT JOIN consultation c ON m.idMedecin = c.idMedecin
GROUP BY m.idMedecin, Civilitemedecin, m.Prenom, m.Nom";

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
        $getDureeMedecinQuery = $pdo->prepare($dureeMedecin);
        $getDureeMedecinQuery->execute();
        $dureeMedecin = $getDureeMedecinQuery->fetchAll(PDO::FETCH_ASSOC);

        http_response_code(200);
        echo json_encode($dureeMedecin);
    }

}

// method not allowed
else {
    http_response_code(405);
    echo json_encode(array("message" => "Method Not Allowed"));
}