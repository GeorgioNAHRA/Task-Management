<?php
session_start();
include('db.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    // Requête pour récupérer l'utilisateur par e-mail
    $query_user = "SELECT * FROM Utilisateur WHERE Email = ?";
    $stmt_user = mysqli_prepare($connection, $query_user);
    mysqli_stmt_bind_param($stmt_user, "s", $mail);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);

    if ($user = mysqli_fetch_assoc($result_user)) {
        // Vérifier le mot de passe haché
        if (password_verify($password, $user['MDP'])) {
            $_SESSION['user_id'] = $user['IDUser'];
            $_SESSION['nom'] = $user['Nom'];
            $_SESSION['prenom'] = $user['Prenom'];
            $_SESSION['mail'] = $user['Email'];
            $_SESSION['statu'] = $user['Statu'];
            $_SESSION['photo'] = $user['photo'];

            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Mot de passe incorrect.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Utilisateur non trouvé.']);
    }
    exit();
}

$user_info = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];

    $query = "SELECT * FROM Utilisateur WHERE IDUser = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_info = mysqli_fetch_assoc($result);

    mysqli_stmt_close($stmt);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MNB - Gestion de projet</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/mnb.css">
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <!-- Logo intégré -->
            <div class="d-flex align-items-center">
                <img src="images/mnb.jpeg" alt="MNB Logo" style="width: 50px; height: 50px; object-fit: contain; margin-right: 15px;">
                <a href="index.php" class="text-white text-decoration-none"><h1 class="h3 mb-0">MNB</h1></a>
            </div>
            <!-- Section utilisateur -->
            <div class="d-flex align-items-center">
                <?php if ($user_info): ?>
                    <span class="me-1">
                        <?php echo htmlspecialchars($user_info['Prenom'] . ' ' . $user_info['Nom']); ?>
                        <?php if (!empty($user_info['photo'])): ?>
                            <img src="pdp/<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                        <?php endif; ?>
                    </span>
                    <!-- Redirection selon le statut de l'utilisateur -->
                    <a href="gestion_projet.php" class="btn btn-light me-2">
                        <?= ($_SESSION['statu'] === 'Admin') ? 'Admin' : 'Espace client'; ?>
                    </a>
                    <a href="compte.php" class="btn btn-outline-light ms-2">Compte</a>
                    <a href="logout.php" class="btn btn-outline-light ms-2">Se déconnecter</a>
                <?php else: ?>
                    <button class="btn btn-outline-light me-1" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">Se connecter</button>
                    <div class="dropdown-menu p-4">
                        <form id="loginForm" method="post">
                            <div id="error-message" style="color: red;"></div>
                            <div class="mb-3">
                                <label for="exampleDropdownFormEmail2" class="form-label">Adresse email</label>
                                <input type="email" class="form-control" id="exampleDropdownFormEmail2" name="mail" placeholder="email@example.com" required>
                            </div>
                            <div class="mb-3">
                                <label for="exampleDropdownFormPassword2" class="form-label">Mot de passe</label>
                                <input type="password" class="form-control" id="exampleDropdownFormPassword2" name="password" placeholder="Mot de passe" required>
                            </div>
                            <button type="submit" class="btn btn-primary">Se connecter</button>
                        </form>
                    </div>
                    <a href="signup.php" class="btn btn-light">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </header>
    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <h1 class="text-center">Bienvenue sur <span>MNB</span></h1>
            <p class="text-center">Votre outil de gestion de projet ultime.</p>
            <div class="row">
                <div class="col-md-6">
                    <div class="card" style="width: 100%;">
                        <img src="images/projet.jpg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title"><span>Projet</span></h5>
                            <p class="card-text">La page de création de projets vous permet d’initier un nouveau projet en saisissant toutes les informations nécessaires.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card" style="width: 104%;">
                        <img src="images/calendrier.jpg" class="card-img-top" alt="...">
                        <div class="card-body">
                            <h5 class="card-title"><span>Calendrier</h5></span>
                            <p class="card-text">La page calendrier permet aux utilisateurs de visualiser et de gérer leurs projets efficacement à l’aide d’un graphique.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/index.js"></script>
</body>
</html>