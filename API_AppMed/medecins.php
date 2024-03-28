<?php
include '../Base/config.php';
include '../API_Auth/check_token.php';
try {
    $pdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}

// Requête SQL pour récupérer un seul medecin
function getSingleMedecin($id)
{
    global $pdo;

    $singlemedecinQuery = $pdo->prepare("SELECT * FROM medecin WHERE idMedecin = ?");
    $singlemedecinQuery->execute([$id]);
    $singlemedecin = $singlemedecinQuery->fetch(PDO::FETCH_ASSOC);

    return $singlemedecin;
}

header('Content-Type: application/json');




/******************* GET *******************/
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Vérifier le token JWT
    if (check_token()) {
        // Requête SQL pour récupérer la liste des medecins
        $getmedecinsQuery = $pdo->prepare("SELECT * FROM medecin");
        $getmedecinsQuery->execute();
        $medecins = $getmedecinsQuery->fetchAll(PDO::FETCH_ASSOC);

        $id = isset ($_SERVER['PATH_INFO']) ? ltrim($_SERVER['PATH_INFO'], '/') : null; // Récupération de l'id depuis l'url

        if ($id) {
            $singlemedecin = getSinglemedecin($id);

            if (!$singlemedecin) {
                http_response_code(404);
                echo json_encode(['message' => 'Aucun medecin trouvé']);
            } else {
                http_response_code(200);
                echo json_encode($singlemedecin);
            }
        } else {
            http_response_code(200);
            echo json_encode($medecins);
        }
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

        //(`Civilite`, `Nom`, `Prenom`)
        $civilite = $data['civilite'];
        $prenom = $data['prenom'];
        $nom = $data['nom'];

        $sql = "INSERT INTO medecin (Civilite, Nom, Prenom) VALUES (?, ?, ?)";

        $stmt = $linkpdo->prepare($sql);

        $test = $stmt->execute([$civilite, $nom, $prenom]);


        // Verification de l'insertion
        if ($stmt->rowCount() > 0) {
            http_response_code(201);
            echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Le medecin a ete ajoute avec succes."));
        } else {
            http_response_code(400);
            echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Une erreur s'est produite lors de l'ajout du medecin."));
        }
    } 
}
/******************* PATCH *******************/
if ($_SERVER['REQUEST_METHOD'] === 'PATCH') {
    // Vérifier le token JWT
    if (check_token()) {
        $data = json_decode(file_get_contents('php://input'), true);
        $medecinId = ltrim($_SERVER['PATH_INFO'], '/');
        $singlemedecin = getSinglemedecin($medecinId);

        if (!$singlemedecin) {
            http_response_code(404);
            echo json_encode(['message' => 'Aucun medecin trouvé']);
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
            $values[] = $medecinId; // Ajoute l'ID du medecin à la fin des valeurs

            $updatemedecinQuery = $pdo->prepare("UPDATE medecin SET $fields WHERE idMedecin = ?");
            $updatemedecinQuery->execute($values);

            if ($updatemedecinQuery->errorCode() != 0) {
                http_response_code(500);
            } else {
                http_response_code(200);
                echo json_encode(['message' => 'medecin modifié']);
            }
        }
    } 
} else {
    http_response_code(405);
}
/******************* DELETE *******************/
if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Vérifier le token JWT
    if (check_token()) {
        $medecinId = ltrim($_SERVER['PATH_INFO'], '/');
        $singlemedecin = getSinglemedecin($medecinId);

        if (!$singlemedecin) {
            http_response_code(404);
            echo json_encode(['message' => 'Aucun medecin trouvé']);
        } else {
            //supprimer toutes les consultations du medecin
            $deleteConsultationsQuery = $pdo->prepare("DELETE FROM consultation WHERE idMedecin = ?");
            $deleteConsultationsQuery->execute([$medecinId]);
            $deletemedecinQuery = $pdo->prepare("DELETE FROM medecin WHERE idMedecin = ?");
            $deletemedecinQuery->execute([$medecinId]);

            if ($deletemedecinQuery->errorCode() != 0) {
                http_response_code(500);
            } else {
                http_response_code(200);
                echo json_encode(['message' => 'medecin supprimé']);
            }
        }
    } 
} else {
    http_response_code(405);
}