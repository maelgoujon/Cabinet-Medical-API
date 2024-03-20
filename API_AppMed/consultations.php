<?php
include '../Base/config.php';
try {
    $pdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Requête SQL pour récupérer une seule consultation
function getSingleConsult($id)
{
    global $pdo;

    $singleConsultQuery = $pdo->prepare("SELECT * FROM consultation WHERE idConsultation = ?");
    $singleConsultQuery->execute([$id]);
    $singleConsult = $singleConsultQuery->fetch(PDO::FETCH_ASSOC);

    return $singleConsult;
}

header('Content-Type: application/json');




/******************* GET *******************/
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Requête SQL pour récupérer la liste des consultations
    $getConsultsQuery = $pdo->prepare("SELECT * FROM consultation");
    $getConsultsQuery->execute();
    $Consults = $getConsultsQuery->fetchAll(PDO::FETCH_ASSOC);

    $id = isset ($_SERVER['PATH_INFO']) ? ltrim($_SERVER['PATH_INFO'], '/') : null; // Récupération de l'id depuis l'url

    if ($id) {
        $singleConsult = getSingleConsult($id);

        if (!$singleConsult) {
            http_response_code(404);
            echo json_encode(['message' => 'Aucune consultation trouvée']);
        } else {
            echo json_encode($singleConsult);
        }
    } else {
        echo json_encode($Consults);
    }
}
// TODO: Corriger la méthode POST
/******************* POST *******************/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    $linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Recuperation des donnees du formulaire HTML
    $data = json_decode(file_get_contents('php://input'), true);

    $idConsultation = $data['idConsultation'];
    $idPatient = $data['id_usager'];
    $idMedecin = $data['id_medecin'];
    $DateConsultation = $data['date_consult'];
    $Heure = $data['heure_consult'];
    $Duree = $data['duree_consult'];

    $sql = "INSERT INTO consultation (`idConsultation`, `idPatient`, `idMedecin`, `DateConsultation`, `Heure`, `Duree`) VALUES (?, ?, ?, ?, ?, ?)";

    $stmt = $linkpdo->prepare($sql);

    $test = $stmt->execute([$idConsultation, $idPatient, $idMedecin, $DateConsultation, $Heure, $Duree]);


    // Verification de l'insertion
    if ($stmt->rowCount() > 0) {
        echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "La consultation a ete ajoute avec succes."));
    } else {
        echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Une erreur s'est produite lors de l'ajout de la consultation."));
    }
}
// TODO: Corriger la méthode PATCH
/******************* PATCH *******************/
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    $data = json_decode(file_get_contents('php://input'), true);
    $ConsultId = ltrim($_SERVER['PATH_INFO'], '/');
    $singleConsult = getSingleConsult($ConsultId);

    if (!$singleConsult) {
        http_response_code(404);
        echo json_encode(['message' => 'Aucune consultation trouvée']);
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
        $values[] = $ConsultId; // Ajoute l'ID de la consult à la fin des valeurs

        $updateConsultQuery = $pdo->prepare("UPDATE patient SET $fields WHERE idPatient = ?");
        $updateConsultQuery->execute($values);

        if ($updateConsultQuery->errorCode() != 0) {
            $errors = $updateConsultQuery->errorInfo();
            echo json_encode(['error' => $errors]);
        } else {
            echo json_encode(['message' => 'Consultation modifiée']);
        }
    }
} else {
    http_response_code(405);
}
// TODO: Corriger la méthode DELETE
/******************* DELETE *******************/
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $ConsultId = ltrim($_SERVER['PATH_INFO'], '/');
    $singleConsult = getSingleConsult($ConsultId);

    if (!$singleConsult) {
        http_response_code(404);
        echo json_encode(['message' => 'Aucune consultation trouvée']);
    } else {
        $deleteConsultQuery = $pdo->prepare("DELETE FROM patient WHERE idPatient = ?");
        $deleteConsultQuery->execute([$ConsultId]);

        if ($deleteConsultQuery->errorCode() != 0) {
            $errors = $deleteConsultQuery->errorInfo();
            echo json_encode(['error' => $errors]);
        } else {
            echo json_encode(['message' => 'Consultation supprimée']);
        }
    }
} else {
    http_response_code(405);
}