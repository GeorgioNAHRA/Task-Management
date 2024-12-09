<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = trim($_POST['mail']);
    $password = trim($_POST['password']);

    // Vérification de l'utilisateur dans la base de données
    $query = "SELECT * FROM Utilisateur WHERE Email = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $mail);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    if ($row = mysqli_fetch_assoc($result)) {
        // Vérification du mot de passe haché
        if (password_verify($password, $row['MDP'])) {
            // Le mot de passe est correct, initialiser la session
            $_SESSION['user_id'] = $row['IDUser'];
            $_SESSION['nom'] = $row['Nom'];
            $_SESSION['prenom'] = $row['Prenom'];
            $_SESSION['photo'] = $row['photo'];
            $_SESSION['statu'] = $row['Statu'];

            header('Location: dashboard.php');
            exit();
        } else {
            $error_message = 'Mot de passe incorrect.';
        }
    } else {
        $error_message = 'Adresse e-mail non trouvée.';
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion</title>
</head>
<body>
    <h1>Connexion</h1>
    <?php if (isset($error_message)): ?>
        <p style="color: red;"><?php echo $error_message; ?></p>
    <?php endif; ?>
    <form method="post" action="">
        <label for="mail">E-mail :</label>
        <input type="email" id="mail" name="mail" required>
        <label for="password">Mot de passe :</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Connexion</button>
    </form>
</body>
</html>
