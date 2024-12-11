<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = trim($_POST['mail']);
    $password = trim($_POST['password']);

    // Requête pour récupérer l'utilisateur par e-mail
    $query_user = "SELECT * FROM Utilisateur WHERE Email = ?";
    $stmt_user = mysqli_prepare($conn, $query_user);
    mysqli_stmt_bind_param($stmt_user, "s", $mail);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if ($user = mysqli_fetch_assoc($result_user)) {
        // Vérifier le mot de passe haché
        if (password_verify($password, $user['MDP'])) {
            // Stocker les données utilisateur dans la session
            $_SESSION['user_id'] = $user['IDUser'];
            $_SESSION['nom'] = $user['Nom'];
            $_SESSION['prenom'] = $user['Prenom'];
            $_SESSION['mail'] = $user['Email'];
            $_SESSION['statu'] = $user['Statu'];
            $_SESSION['photo'] = $user['photo'];

            // Rediriger vers index.php après une connexion réussie
            header('Location: index.php');
            exit();
        } else {
            $error_message = 'Mot de passe incorrect.';
        }
    } else {
        $error_message = 'Utilisateur non trouvé.';
    }
}
?>