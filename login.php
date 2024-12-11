<?php
session_start();
include('../MNB/db.php');

header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    // Récupérer l'utilisateur avec l'e-mail fourni
    $query_user = "SELECT * FROM Utilisateur WHERE Email = ?";
    $stmt_user = mysqli_prepare($conn, $query_user);
    mysqli_stmt_bind_param($stmt_user, "s", $mail);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if ($user = mysqli_fetch_assoc($result_user)) {
        // Vérification du mot de passe haché
        if (password_verify($password, $user['MDP'])) {
            $_SESSION['user_id'] = $user['IDUser'];
            $_SESSION['nom'] = $user['Nom'];
            $_SESSION['prenom'] = $user['Prenom'];
            $_SESSION['mail'] = $user['Email'];
            $_SESSION['statu'] = $user['Statu'];

            if ($user['Statu'] === 'Admin') {
                $_SESSION['admin_id'] = $user['IDUser'];
            }

            $response['status'] = 'success';
        } else {
            $response['status'] = 'error';
            $response['message'] = 'Mot de passe incorrect.';
        }
    } else {
        $response['status'] = 'error';
        $response['message'] = 'Utilisateur non trouvé.';
    }
} else {
    $response['status'] = 'error';
    $response['message'] = 'Méthode non autorisée.';
}

echo json_encode($response);
?>