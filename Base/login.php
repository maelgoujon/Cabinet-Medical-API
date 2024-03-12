<?php
require_once '../API_Auth/getjwt.php';
// Démarrer la session
session_start();

// Vérifier si l'utilisateur est déjà connecté, si oui le rediriger vers la page d'accueil grace au JWT
if (isset($_SESSION["jwt"])) {
    header("Location: ../index.php");
    exit;
}
// si l'utilisateur n'a pas de JWT alors on en genere un avec getjwt.php
else {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $login = $_POST["login"];
        $password = $_POST["password"];
        $jwt = get_jwt($login, $password);
        if ($jwt) {
            $_SESSION["jwt"] = $jwt;
            header("Location: ../index.php");
            exit;
        } else {
            $error_message = "Nom d'utilisateur ou mot de passe incorrect";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="../Base/bootstrap.min.css" rel="stylesheet" />
    <link href="../Base/accueil.css" rel="stylesheet" />
    <link href="../Base/style.css" rel="stylesheet" />
    <script src="../Base/jquery-3.2.1.slim.min.js"></script>
    <script src="../Base/popper.min.js"></script>
    <script src="../Base/bootstrap.bundle.min.js"></script>
    <link rel="icon" type="image/png" href="../Images/logo.png" />
    <title>Authentification</title>
</head>

<body> <!-- Ajout de la classe container -->
    <div class="container">
        <div class="text-center"> <!-- Centrer le contenu -->
            <h2 class="mt-5">Authentification</h2>
            <?php if (isset($error_message)) { ?>
                <div class="alert alert-danger">
                    <?php echo $error_message; ?>
                </div>
            <?php } ?>
            <form method="post" action="">
                <div class="form-outline mb-4">
                    <label class="form-label" for="login">Nom d'utilisateur :</label>
                    <input type="text" name="login" class="form-control" />
                </div>

                <!-- Password input -->
                <div class="form-outline mb-4">
                    <label class="form-label" for="password">Mot de passe :</label>
                    <input type="password" name="password" class="form-control" />
                </div>
                <br>
                <input type="submit" value="Se connecter" class="btn btn-primary">
            </form>
        </div>
    </div>

</body>

</html>