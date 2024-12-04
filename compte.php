<?php
session_start();
include 'db.php';

$error_message = '';

// Handle form submissions
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
            header('Location: MNB.php?photo_deleted=1');
            exit();
        }

        // Mise à jour des informations utilisateur
        $nom = trim($_POST['nom']);
        $prenom = trim($_POST['prenom']);
        $mail = $user_info['Email']; // Préserver l'e-mail d'origine

        $query = "UPDATE Utilisateur SET Nom=?, Prenom=? WHERE IDUser=?";
        $stmt = mysqli_prepare($connection, $query);
        mysqli_stmt_bind_param($stmt, "sss", $nom, $prenom, $user_id);

        if (mysqli_stmt_execute($stmt)) {
            // Gestion de l'upload de photo
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

            header('Location: MNB.php?success=1');
            exit();
        } else {
            $error_message = 'Une erreur s\'est produite lors de la mise à jour des informations.';
        }
    }
}

// Récupérer les informations utilisateur si connecté
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
    <link rel="stylesheet" href="MNB.css">
    <style>
        .readonly-email {
            background-color: #e9ecef;
            pointer-events: none;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="MNB.php" class="text-white text-decoration-none"><h1 class="h3 mb-0">MNB</h1></a>
                <nav>
                    <ul class="nav">
                        <!-- Nav items -->
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
                        <a href="compte.php" class="btn btn-outline-light ms-2">Compte</a>
                        <a href="logout.php" class="btn btn-outline-light ms-2">Se déconnecter</a>
                    <?php else: ?>
                        <a href="signup.php" class="btn btn-light">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-5">
        <div class="container">
            <?php if ($error_message) : ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <form class="row g-3 needs-validation" novalidate method="post" action="" enctype="multipart/form-data">
                <div class="col-md-4">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo isset($user_info['Nom']) ? $user_info['Nom'] : ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo isset($user_info['Prenom']) ? $user_info['Prenom'] : ''; ?>" required>
                </div>
                <div class="col-md-4">
                    <label for="mail" class="form-label">E-mail address</label>
                    <input type="email" class="form-control readonly-email" id="mail" name="mail" value="<?php echo isset($user_info['Email']) ? $user_info['Email'] : ''; ?>" readonly>
                    <small class="text-muted">Votre adresse e-mail ne peut pas être modifiée.</small>
                </div>
                <div class="col-md-4">
                    <label for="photo" class="form-label">Photo de profil</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                </div>
                <div class="col-12 d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <p class="text-center">&copy; 2024 MNB. Tous droits réservés.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>