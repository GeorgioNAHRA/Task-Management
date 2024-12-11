<?php
session_start();
include 'db.php';

$error_message = '';

// Gestion des soumissions du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_SESSION['user_id'])) {
        $user_id = $_SESSION['user_id'];

        if (isset($_POST['delete_photo'])) {
            // Suppression de la photo actuelle
            $query = "SELECT photo FROM Utilisateur WHERE IDUser = ?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "s", $user_id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $user_info = mysqli_fetch_assoc($result);

            $current_photo = $user_info['photo'];
            if ($current_photo !== 'default.png') {
                $photo_path = "pdp/" . $current_photo;
                if (file_exists($photo_path)) {
                    unlink($photo_path);
                }
            }

            $default_photo = 'default.png';
            $update_photo_query = "UPDATE Utilisateur SET photo=? WHERE IDUser=?";
            $update_photo_stmt = mysqli_prepare($connection, $update_photo_query);
            mysqli_stmt_bind_param($update_photo_stmt, "ss", $default_photo, $user_id);
            mysqli_stmt_execute($update_photo_stmt);
            header('Location: compte.php?photo_deleted=1');
            exit();
        }

        // Mise à jour des informations utilisateur
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $mail = trim($_POST['mail']);

        // Vérification de l'unicité de l'e-mail
        $query_check_email = "SELECT * FROM Utilisateur WHERE Email = ? AND IDUser != ?";
        $stmt_check_email = mysqli_prepare($connection, $query_check_email);
        mysqli_stmt_bind_param($stmt_check_email, "ss", $mail, $user_id);
        mysqli_stmt_execute($stmt_check_email);
        $result_check_email = mysqli_stmt_get_result($stmt_check_email);

        if (mysqli_num_rows($result_check_email) > 0) {
            $error_message = 'Cette adresse e-mail est déjà utilisée par un autre utilisateur.';
        } else {
            // Mise à jour des informations dans la base de données
            $query = "UPDATE Utilisateur SET Nom=?, Prenom=?, Email=? WHERE IDUser=?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "ssss", $nom, $prenom, $mail, $user_id);

            if (mysqli_stmt_execute($stmt)) {
                // Mise à jour de la photo
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
                    $upload_dir = 'pdp/';
                    $photo = uniqid() . '_' . basename($_FILES['photo']['name']);
                    $uploaded_file = $upload_dir . $photo;

                    // Création du dossier si nécessaire
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    // Suppression de l'ancienne photo si elle n'est pas par défaut
                    $query = "SELECT photo FROM Utilisateur WHERE IDUser = ?";
                    $stmt = mysqli_prepare($connection, $query);
                    mysqli_stmt_bind_param($stmt, "s", $user_id);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);
                    $user_info = mysqli_fetch_assoc($result);

                    $current_photo = $user_info['photo'];
                    if ($current_photo !== 'default.png') {
                        $photo_path = "pdp/" . $current_photo;
                        if (file_exists($photo_path)) {
                            unlink($photo_path);
                        }
                    }

                    // Upload de la nouvelle photo
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploaded_file)) {
                        $update_photo_query = "UPDATE Utilisateur SET photo=? WHERE IDUser=?";
                        $update_photo_stmt = mysqli_prepare($connection, $update_photo_query);
                        mysqli_stmt_bind_param($update_photo_stmt, "ss", $photo, $user_id);
                        mysqli_stmt_execute($update_photo_stmt);
                    } else {
                        $error_message = "Échec du téléchargement de la photo.";
                    }
                }

                // Mise à jour du mot de passe si un nouveau est fourni
                if (isset($_POST['password']) && !empty($_POST['password'])) {
                    $new_password = trim($_POST['password']);
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

                    $update_password_query = "UPDATE Utilisateur SET MDP=? WHERE IDUser=?";
                    $stmt_password = mysqli_prepare($connection, $update_password_query);
                    mysqli_stmt_bind_param($stmt_password, "ss", $hashed_password, $user_id);

                    if (!mysqli_stmt_execute($stmt_password)) {
                        $error_message = "Une erreur s'est produite lors de la mise à jour du mot de passe.";
                    }
                }

                header('Location: compte.php?success=1');
                exit();
            } else {
                $error_message = 'Une erreur s\'est produite lors de la mise à jour des informations.';
            }
        }
    }
}

// Récupération des informations utilisateur si connecté
$user_info = null;
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $query = "SELECT * FROM Utilisateur WHERE IDUser = ?";
    $stmt = mysqli_prepare($connection, $query);
    mysqli_stmt_bind_param($stmt, "s", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user_info = mysqli_fetch_assoc($result);
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MNB - Gestion de projet</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/mnb.css">
</head>
<body>
<header class="bg-dark text-white py-3">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center">
            <div class="d-flex align-items-center">
                <img src="images/mnb.jpeg" alt="MNB Logo" style="width: 50px; height: 50px; object-fit: contain; margin-right: 15px;">
                <a href="index.php" class="text-white text-decoration-none"><h1 class="h3 mb-0">MNB</h1></a>
            </div>
            <div class="d-flex align-items-center">
                <?php if ($user_info): ?>
                    <span class="me-1">
                        <?php echo htmlspecialchars($user_info['Prenom'] . ' ' . $user_info['Nom']); ?>
                        <?php if (!empty($user_info['photo'])): ?>
                            <img src="pdp/<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                        <?php endif; ?>
                    </span>
                    <a href="logout.php" class="btn btn-outline-light ms-2">Se déconnecter</a>
                <?php else: ?>
                    <a href="signup.php" class="btn btn-light">S'inscrire</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <?php if ($error_message): ?>
            <div class="alert alert-danger">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        <form class="row g-3 needs-validation" novalidate method="post" action="" enctype="multipart/form-data">
            <div class="col-md-4">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" value="<?= htmlspecialchars($user_info['Nom'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label for="prenom" class="form-label">Prénom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" value="<?= htmlspecialchars($user_info['Prenom'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label for="mail" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="mail" name="mail" value="<?= htmlspecialchars($user_info['Email'] ?? '') ?>" required>
            </div>
            <div class="col-md-4">
                <label for="photo" class="form-label">Photo de profil</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Mot de passe</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Nouveau mot de passe">
            </div>
            <div class="col-12 d-flex justify-content-start gap-2">
                <button type="submit" class="btn btn-primary">Enregistrer</button>
            </div>
        </form>
    </div>
</main>

<footer class="bg-dark text-white py-5">
    <div class="container">
        <p class="text-center">&copy; 2024 MNB. Tous droits réservés.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>