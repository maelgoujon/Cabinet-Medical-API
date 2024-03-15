<?php
include '../Base/config.php';   // Inclusion de la connexion à la base de données
header('Content-Type: application/json');

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    $linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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
            echo json_encode(array("status" => "success", "status_code" => 200, "status_message" => "Le patient a ete ajoute avec succes."));
        } else {
            echo json_encode(array("status" => "error", "status_code" => 400, "status_message" => "Une erreur s'est produite lors de l'ajout du patient."));
        }
    }


} catch (PDOException $e) {
    die('Erreur : ' . $e->getMessage());
}
?>