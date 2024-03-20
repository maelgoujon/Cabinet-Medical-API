<?php
session_start();

// Connexion au serveur MySQL
include '../Base/config.php';

try {
    $linkpdo = new PDO("mysql:host=$server;dbname=$db", $login, $mdp);
    // Définition du mode d'erreur PDO à exception
    $linkpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Récupération de l'identifiant du contact à supprimer depuis l'URL
    $idPatient = $_GET['id'];

    // Requête SQL pour supprimer le contact et toute ses consultations
    $sql = "DELETE FROM patient WHERE idPatient = ?";
    $sql2 = "DELETE FROM consultation WHERE idPatient = ?";

    // Préparation de la requête
    $stmt2 = $linkpdo->prepare($sql2);
    $stmt = $linkpdo->prepare($sql);

    // Exécution de la requête avec l'identifiant du contact
    $stmt2->execute([$idPatient]);
    $stmt->execute([$idPatient]);

    // Fermeture de la connexion à la base de données
    $linkpdo = null;

    // Redirection vers la page recherche.php après la suppression
    header("Location: index.php");
    exit;
} catch (PDOException $e) {
    $error_message = 'Erreur : ' . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>

  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Statistiques</title>

</head>

<body>
  <?php
  if (isset($error_message)) {
      echo $error_message;
  }
  ?>
</body>

</html>