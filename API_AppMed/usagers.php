<?php
include '../Base/config.php';
include 'check_remote_jwt.php';
try {
    $pdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Requête SQL pour récupérer un seul patient
function getSinglePatient($id)
{
    global $pdo;

    $singlePatientQuery = $pdo->prepare("SELECT * FROM patient WHERE idPatient = ?");
    $singlePatientQuery->execute([$id]);
    $singlePatient = $singlePatientQuery->fetch(PDO::FETCH_ASSOC);

    return $singlePatient;
}

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
        // Requête SQL pour récupérer la liste des patients
        $getPatientsQuery = $pdo->prepare("SELECT * FROM patient");
        $getPatientsQuery->execute();
        $patients = $getPatientsQuery->fetchAll(PDO::FETCH_ASSOC);

        $id = isset($_SERVER['PATH_INFO']) ? ltrim($_SERVER['PATH_INFO'], '/') : null; // Récupération de l'id depuis l'url

        if ($id) {
            $singlePatient = getSinglePatient($id);

            if (!$singlePatient) {
                http_response_code(404);
                echo json_encode(['message' => 'Aucun patient trouvé']);
            } else {
                http_response_code(200);
                echo json_encode($singlePatient);
            }
        } else {
            http_response_code(200);
            echo json_encode($patients);
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Accès refusé"));
    }
}
/******************* POST *******************/
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Vérifier le token JWT
    if (check_jwt_ok()) {
        $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
        $linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        // Recuperation des donnees du formulaire HTML
        $data = json_decode(file_get_contents('php://input'), true);

        $civilite = $data['civilite'];
        $prenom = $data['prenom'];
        $nom = $data['nom'];
        $adresse = $data['adresse'];
        $code_postal = $data['code_postal'];
        $ville = $data['ville'];
        $date_nais = $data['date_nais'];
        $lieu_nais = $data['lieu_nais'];
        $num_secu = $data['num_secu'];
        $id_medecin = $data['id_medecin'];

        $sql = "INSERT INTO patient (`Civilite`, `Prenom`, `Nom`, `Adresse`, `Ville`, `Code_postal`, `Date_de_naissance`, `Lieu_de_naissance`, `Numero_Securite_Sociale`, `idMedecin`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $linkpdo->prepare($sql);

        $test = $stmt->execute([$civilite, $prenom, $nom, $adresse, $ville, $code_postal, $date_nais, $lieu_nais, $num_secu, $id_medecin]);


        // Verification de l'insertion
        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Le patient a ete ajoute avec succes."));
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Une erreur s'est produite lors de l'ajout du patient."));
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Accès refusé"));
    }
}
/******************* PATCH *******************/
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // Vérifier le token JWT
    if (check_jwt_ok()) {
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
                $column = isset($columnMapping[$key]) ? $columnMapping[$key] : $key;
                $fields .= "$column = ?,";
                $values[] = $value;
            }
            $fields = rtrim($fields, ','); // Supprime la dernière virgule
            $values[] = $patientId; // Ajoute l'ID du patient à la fin des valeurs

            $updatePatientQuery = $pdo->prepare("UPDATE patient SET $fields WHERE idPatient = ?");
            $updatePatientQuery->execute($values);

            if ($updatePatientQuery->errorCode() != 0) {
                http_response_code(500);
            } else {
                http_response_code(200);
                echo json_encode(['message' => 'Patient modifié']);
            }
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Accès refusé"));
    }
}
/******************* DELETE *******************/
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Vérifier le token JWT
    if (check_jwt_ok()) {
        $patientId = ltrim($_SERVER['PATH_INFO'], '/');
        $singlePatient = getSinglePatient($patientId);

        if (!$singlePatient) {
            http_response_code(404);
            echo json_encode(['message' => 'Aucun patient trouvé']);
        } else {
            //supprimer toutes les consultations du patient
            $deleteConsultationsQuery = $pdo->prepare("DELETE FROM consultation WHERE idPatient = ?");
            $deleteConsultationsQuery->execute([$patientId]);
            $deletePatientQuery = $pdo->prepare("DELETE FROM patient WHERE idPatient = ?");
            $deletePatientQuery->execute([$patientId]);

            if ($deletePatientQuery->errorCode() != 0) {
                http_response_code(500);
            } else {
                http_response_code(200);
                echo json_encode(['message' => 'Patient supprimé']);
            }
        }
    } else {
        http_response_code(401);
        echo json_encode(array("message" => "Accès refusé"));
    }
} else {
    http_response_code(405);
}