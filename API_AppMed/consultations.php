<?php
require '../API_Auth/check_token.php';
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
    // Vérifier le token JWT
    if (check_token()) {
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
                http_response_code(200);
                echo json_encode($singleConsult);
            }
        } else {
            http_response_code(200);
            echo json_encode($Consults);
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Unauthorized"));
    }
}
/******************* POST *******************/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier le token JWT
    if (check_token()) {
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

        // Vérifier si la consultation existe déjà
        $checkSql = "SELECT * FROM consultation WHERE idPatient = ? AND DateConsultation = ? AND Heure = ?";
        $checkStmt = $linkpdo->prepare($checkSql);
        $checkStmt->execute([$idPatient, $DateConsultation, $Heure]);
        if ($checkStmt->rowCount() > 0) {
            http_response_code(400);
            echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "La consultation existe deja."));
            exit;
        }

        $sql = "INSERT INTO consultation (`idConsultation`, `idPatient`, `idMedecin`, `DateConsultation`, `Heure`, `Duree`) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $linkpdo->prepare($sql);
        $test = $stmt->execute([$idConsultation, $idPatient, $idMedecin, $DateConsultation, $Heure, $Duree]);

        // Verification de l'insertion
        if ($stmt->rowCount() > 0) {
            http_response_code(200);
            echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "La consultation a ete ajoute avec succes."));
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Une erreur s'est produite lors de l'ajout de la consultation."));
        }
    } else {
        http_response_code(401);
    }
}
/******************* PATCH *******************/
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // Vérifier le token JWT
    if (check_token()) {
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
                'id_usager' => 'idPatient',
                'id_medecin' => 'idMedecin',
                'date_consult' => 'DateConsultation',
                'heure_consult' => 'Heure',
                'duree_consult' => 'Duree'
            ];
            foreach ($data as $key => $value) {
                // Utiliser la correspondance si elle existe
                $column = isset ($columnMapping[$key]) ? $columnMapping[$key] : $key;
                $fields .= "$column = ?,";
                $values[] = $value;
            }
            $fields = rtrim($fields, ','); // Supprime la dernière virgule
            $values[] = $ConsultId; // Ajoute l'ID de la consult à la fin des valeurs

            $updateConsultQuery = $pdo->prepare("UPDATE consultation SET $fields WHERE idConsultation = ?");
            $updateConsultQuery->execute($values);

            if ($updateConsultQuery->errorCode() != 0) {
                http_response_code(500);
                $errors = $updateConsultQuery->errorInfo();
                echo json_encode(['error' => $errors]);
            } else {
                http_response_code(200);
                echo json_encode(['message' => 'Consultation modifiée']);
            }
        }
    } else {
        http_response_code(401);

    }
} else {
    http_response_code(405);
}
/******************* DELETE *******************/
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    $ConsultId = ltrim($_SERVER['PATH_INFO'], '/');
    $singleConsult = getSingleConsult($ConsultId);

    if (!$singleConsult) {
        http_response_code(404);
        echo json_encode(['message' => 'Aucune consultation trouvée']);
    } else {
        $deleteConsultQuery = $pdo->prepare("DELETE FROM consultation WHERE idConsultation = ?");
        $deleteConsultQuery->execute([$ConsultId]);

        if ($deleteConsultQuery->errorCode() != 0) {
            $errors = $deleteConsultQuery->errorInfo();
            http_response_code(500);
            echo json_encode(['error' => $errors]);
        } else {
            http_response_code(200);
            echo json_encode(['message' => 'Consultation supprimée']);
        }
    }
} else {
    http_response_code(405);
}