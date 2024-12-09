<?php
session_start();
include('db.php');

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
        $_SESSION['photo'] = $user['photo'];

        if ($user['Statu'] === 'Admin') {
            $_SESSION['admin_id'] = $user['IDUser'];
        }

        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Identifiants incorrects']);
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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="MNB.css">
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="MNB.php" class="text-white text-decoration-none"><h1 class="h3 mb-0">MNB</h1></a>
                <nav>
                    <ul class="nav">
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Produit</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Documents</a></li>
                                <li><a class="dropdown-item" href="#">Projets</a></li>
                                <li><a class="dropdown-item" href="#">Calendrier</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Ressources</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Blog</a></li>
                                <li><a class="dropdown-item" href="#">MNB Académie</a></li>
                                <li><a class="dropdown-item" href="#">Site d'aide</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Solution</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Grandes Entreprises</a></li>
                                <li><a class="dropdown-item" href="#">Petites équipes et PME</a></li>
                                <li><a class="dropdown-item" href="#">Individuel</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Télécharger</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">MNB</a></li>
                                <li><a class="dropdown-item" href="#">MNB Calendrier</a></li>
                                <li><a class="dropdown-item" href="#">Clipper</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a href="#" class="nav-link text-white">Tarifs</a></li>
                    </ul>
                </nav>
                <div class="d-flex align-items-center">
                    <?php if ($user_info): ?>
                        <span class="me-1">
                            <?php echo htmlspecialchars($user_info['Prenom'] . ' ' . $user_info['Nom']); ?>
                            <?php if (!empty($user_info['photo'])): ?>
                                <img src="pdp/<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                            <?php endif; ?>
                        </span>
                        <?php if (isset($_SESSION['admin_id'])): ?>
                            <a href="dashboard.php" class="btn btn-light me-2">Admin</a>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['statu']) && ($_SESSION['statu'] === 'User' || $_SESSION['statu'] === 'Admin')): ?>
                            <a href="Essayer_MNB.php" class="btn btn-light me-1">Espace Client</a>
                        <?php endif; ?>
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
                    <img src="accueil.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title"><span>Projet</span></h5>
                        <p class="card-text">Dans l'ensemble, la page projet semble être une introduction à un projet spécifique sur la plateforme "MNB", offrant aux utilisateurs un aperçu rapide du projet et la possibilité d'en savoir plus ou de s'y engager davantage.</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card" style="width: 104%;">
                    <img src="gestion.jpg" class="card-img-top" alt="...">
                    <div class="card-body">
                        <h5 class="card-title"><span>Gestion</h5></span>
                        <p class="card-text">la page de gestion des clients serait un espace central où les utilisateurs peuvent visualiser, ajouter, modifier et supprimer des informations sur leurs clients, ainsi que réaliser diverses actions de gestion liées à leur base de clients.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
  <figcaption class="figure-caption"></figcaption>
</figure>
</main>


    <!-- Footer -->
    <?php
    include('footer.php');
    ?>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="scripts.js"></script>
    <script>
        document.getElementById('loginForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(this);
            const xhr = new XMLHttpRequest();
            xhr.open('POST', '', true);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        window.location.href = 'MNB.php';
                    } else {
                        document.getElementById('error-message').innerText = response.message;
                    }
                }
            };
            xhr.send(formData);
        });
    </script>
</body>
</html>

