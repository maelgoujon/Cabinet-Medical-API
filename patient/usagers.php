<?php
include '../Base/config.php';
try {
    $pdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Requête SQL pour récupérer la liste des patients
$getPatientsQuery = $pdo->prepare("SELECT * FROM patient");
$getPatientsQuery->execute();
$patients = $getPatientsQuery->fetchAll(PDO::FETCH_ASSOC);

// Requête SQL pour récupérer un seul patient
function getSinglePatient($id)
{
    global $pdo;

    $singlePatientQuery = $pdo->prepare("SELECT * FROM patient WHERE idPatient = ?");
    $singlePatientQuery->execute([$id]);
    $singlePatient = $singlePatientQuery->fetch(PDO::FETCH_ASSOC);

    return $singlePatient;
}

// API pour récupérer la liste des patients ou un seul patient
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if (isset ($_SERVER['PATH_INFO'])) {
        $patientId = ltrim($_SERVER['PATH_INFO'], '/');
        $singlePatient = getSinglePatient($patientId);

        if (!$singlePatient) {
            http_response_code(404);
            echo json_encode(['message' => 'Aucun patient trouvé']);
        } else {
            echo json_encode($singlePatient);
        }
    } else {
        echo json_encode($patients);
    }
}
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $data = json_decode(file_get_contents('php://input'), true);
    $patientId = ltrim($_SERVER['PATH_INFO'], '/');
    $singlePatient = getSinglePatient($patientId);

    if (!$singlePatient) {
        http_response_code(404);
        echo json_encode(['message' => 'Aucun patient trouvé']);
    } else {
        $fields = '';
        $values = [];
        // Colonnes avec des noms différents
        $columnMapping = [
            'id_medecin' => 'idMedecin',
        ];
        foreach ($data as $key => $value) {
            // Utiliser la correspondance si elle existe
            $column = isset ($columnMapping[$key]) ? $columnMapping[$key] : $key;
            $fields .= "$column = ?,";
            $values[] = $value;
        }
        $fields = rtrim($fields, ','); // Supprime la dernière virgule
        $values[] = $patientId; // Ajoute l'ID du patient à la fin des valeurs

        $updatePatientQuery = $pdo->prepare("UPDATE patient SET $fields WHERE idPatient = ?");
        $updatePatientQuery->execute($values);

        if ($updatePatientQuery->errorCode() != 0) {
            $errors = $updatePatientQuery->errorInfo();
            echo json_encode(['error' => $errors]);
        } else {
            echo json_encode(['message' => 'Patient modifié']);
        }
    }
} else {
    http_response_code(405);
}