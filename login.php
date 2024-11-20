<?php
session_start();
include('../Gate/db.php');

header('Content-Type: application/json');

$response = array();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    $query_user = "SELECT * FROM Utilisateur WHERE Email = ? AND MDP = ?";
    $stmt_user = mysqli_prepare($connection, $query_user);
    mysqli_stmt_bind_param($stmt_user, "ss", $mail, $password);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if ($user = mysqli_fetch_assoc($result_user)) {
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
        $response['message'] = 'Identifiants incorrects';
    }
}

echo json_encode($response);
?>