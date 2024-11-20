<?php
session_start();
include 'db.php';

$error_message = '';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['confirm_delete'])) {
        // Handle account deletion
        $user_id = $_SESSION['user_id'];
        $delete_query = "DELETE FROM Utilisateur WHERE IDUser = ?";
        $delete_stmt = mysqli_prepare($connection, $delete_query);
        mysqli_stmt_bind_param($delete_stmt, "s", $user_id);
        mysqli_stmt_execute($delete_stmt);

        session_unset();
        session_destroy();

        header('Location: index.php');
        exit();
    } elseif (isset($_SESSION['user_id'])) {
        // Handle profile update
        $user_id = $_SESSION['user_id'];

        if (isset($_POST['delete_photo'])) {
            // Fetch the current photo to delete it
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

            // Set photo to default.png
            $default_photo = 'default.png';
            $update_photo_query = "UPDATE Utilisateur SET photo=? WHERE IDUser=?";
            $update_photo_stmt = mysqli_prepare($connection, $update_photo_query);
            mysqli_stmt_bind_param($update_photo_stmt, "ss", $default_photo, $user_id);
            if (mysqli_stmt_execute($update_photo_stmt)) {
                mysqli_stmt_close($update_photo_stmt);
                header('Location: gate.php?photo_deleted=1');
                exit();
            } else {
                $error_message = 'Une erreur s\'est produite lors de la suppression de la photo.';
            }
        } else {
            $nom = $_POST['nom'];
            $prenom = $_POST['prenom'];
            $mail = $_POST['mail'];

            $query = "UPDATE Utilisateur SET Nom=?, Prenom=?, Email=? WHERE IDUser=?";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "ssss", $nom, $prenom, $mail, $user_id);

            if (mysqli_stmt_execute($stmt)) {
                // Handle the file upload
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
                    $upload_dir = 'pdp/';
                    $uploaded_file = $upload_dir . basename($_FILES['photo']['name']);

                    // Ensure the upload directory exists
                    if (!is_dir($upload_dir)) {
                        mkdir($upload_dir, 0777, true);
                    }

                    // Move the uploaded file to the target directory
                    if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploaded_file)) {
                        $photo = basename($_FILES['photo']['name']);
                        $update_photo_query = "UPDATE Utilisateur SET photo=? WHERE IDUser=?";
                        $update_photo_stmt = mysqli_prepare($connection, $update_photo_query);
                        mysqli_stmt_bind_param($update_photo_stmt, "ss", $photo, $user_id);
                        mysqli_stmt_execute($update_photo_stmt);
                        mysqli_stmt_close($update_photo_stmt);
                    } else {
                        $error_message = "Échec du téléchargement de la photo.";
                    }
                }
                header('Location: gate.php?success=1');
                exit();
            } else {
                $error_message = 'Une erreur s\'est produite lors de la mise à jour des informations.';
            }

            mysqli_stmt_close($stmt);
        }
    } else {
        // Handle registration
        $nom = $_POST['nom'];
        $prenom = $_POST['prenom'];
        $mail = $_POST['mail'];
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        $photo = 'default.png'; // Default profile photo

        // Check if email is already used
        $query_check_email = "SELECT * FROM Utilisateur WHERE Email = ?";
        $stmt_check_email = mysqli_prepare($connection, $query_check_email);
        mysqli_stmt_bind_param($stmt_check_email, "s", $mail);
        mysqli_stmt_execute($stmt_check_email);
        $result_check_email = mysqli_stmt_get_result($stmt_check_email);

        if (mysqli_num_rows($result_check_email) > 0) {
            $error_message = 'Cette adresse e-mail est déjà utilisée.';
        } elseif ($password !== $password_confirm) {
            $error_message = 'Les mots de passe ne correspondent pas.';
        } elseif (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'L\'adresse email n\'est pas valide.';
        } else {
            $Statu = 'en attente';

            // Handle the file upload
            if (isset($_FILES['photo']) && $_FILES['photo']['error'] == UPLOAD_ERR_OK) {
                $upload_dir = 'pdp/';
                $uploaded_file = $upload_dir . basename($_FILES['photo']['name']);

                // Ensure the upload directory exists
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploaded_file)) {
                    $photo = basename($_FILES['photo']['name']);
                } else {
                    $error_message = "Échec du téléchargement de la photo.";
                }
            }

            // Insert user data into the database
            $query = "INSERT INTO Utilisateur (Email, MDP, Nom, Prenom, Statu, photo) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = mysqli_prepare($connection, $query);
            mysqli_stmt_bind_param($stmt, "ssssss", $mail, $password, $nom, $prenom, $Statu, $photo);

            if (mysqli_stmt_execute($stmt)) {
                $userId = mysqli_insert_id($connection);
                $_SESSION['user_id'] = $userId;
                $_SESSION['nom'] = $nom;
                $_SESSION['prenom'] = $prenom;
                $_SESSION['mail'] = $mail;
                header('Location: gate.php');
                exit();
            } else {
                $error_message = 'Une erreur s\'est produite lors de l\'inscription.';
            }
        }
    }
}

// Fetch user info for the form if logged in
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

if (isset($_GET['success']) && $_GET['success'] == '1') {
    $success_message = "Vos informations ont été mises à jour avec succès.";
}

if (isset($_GET['photo_deleted']) && $_GET['photo_deleted'] == '1') {
    $photo_deleted_message = "Votre photo de profil a été supprimée avec succès.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate - Gestion de projet</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="GATE.css">
    <style>
        .disabled-email {
            background-color: #e9ecef;
            cursor: not-allowed;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="gate.php" class="text-white text-decoration-none"><h1 class="h3 mb-0">Gate</h1></a>
                <nav>
                    <ul class="nav">
                        <!-- Nav items here -->
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
                            <a href="index.php" class="btn btn-light me-2">Admin</a>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['statu']) && ($_SESSION['statu'] === 'accepter' || $_SESSION['statu'] === 'Admin')): ?>
                            <a href="Essayer_gate.php" class="btn btn-light me-1">Essayer Gate</a>
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
            <?php if ($error_message) : ?>
                <p style="color: red;"><?php echo $error_message; ?></p>
            <?php endif; ?>
            <?php if (isset($success_message)) : ?>
                <p style="color: green;"><?php echo $success_message; ?></p>
            <?php endif; ?>
            <?php if (isset($photo_deleted_message)) : ?>
                <p style="color: green;"><?php echo $photo_deleted_message; ?></p>
            <?php endif; ?>
            <form class="row g-3 needs-validation" novalidate method="post" action="" enctype="multipart/form-data">
                <div class="col-md-4">
                    <label for="nom" class="form-label">Nom</label>
                    <input type="text" class="form-control" id="nom" name="nom" value="<?php echo isset($user_info['Nom']) ? $user_info['Nom'] : ''; ?>" required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="prenom" class="form-label">Prénom</label>
                    <input type="text" class="form-control" id="prenom" name="prenom" value="<?php echo isset($user_info['Prenom']) ? $user_info['Prenom'] : ''; ?>" required>
                    <div class="valid-feedback">
                        Looks good!
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="mail" class="form-label">E-mail address</label>
                    <input type="email" class="form-control disabled-email" id="mail" name="mail" value="<?php echo isset($user_info['Email']) ? $user_info['Email'] : ''; ?>" disabled>
                    <div class="invalid-feedback">
                        Veuillez fournir une adresse valide.
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="photo" class="form-label">Photo de profil</label>
                    <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
                </div>
                <?php if ($user_info && $user_info['photo'] !== 'default.png'): ?>
                <div class="col-md-12">
                    <img src="pdp/<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Photo de profil" width="150"><br>
                </div>
                <?php endif; ?>
                <div class="col-12 d-flex justify-content-start gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <?php if ($user_info && $user_info['photo'] !== 'default.png'): ?>
                        <form method="post" action="" class="d-inline">
                            <input type="hidden" name="delete_photo" value="1">
                            <button type="submit" class="btn btn-danger">Supprimer la photo de profil</button>
                        </form>
                    <?php endif; ?>
                    <form method="post" action="" class="d-inline">
                        <input type="hidden" name="confirm_delete" value="1">
                        <button type="button" class="btn btn-danger" onclick="confirmDelete()">Supprimer mon compte</button>
                    </form>
                </div>
            </form>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <h5>Gate</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Accueil</a></li>
                        <li><a href="#" class="text-white text-decoration-none">À propos</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Contact</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Blog</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Produit</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Fonctionnalités</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Tarifs</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Intégrations</a></li>
                        <li><a href="#" class="text-white text-decoration-none">API</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Ressources</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">Documentation</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Guides</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Support</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Communauté</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Entreprise</h5>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white text-decoration-none">À propos</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Carrières</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Presse</a></li>
                        <li><a href="#" class="text-white text-decoration-none">Blog</a></li>
                    </ul>
                </div>
                <div class="col-md-3">
                    <h5>Suivez-nous</h5>
                    <ul class="list-unstyled d-flex gap-2">
                        <li><a href="#" class="text-white"><i class="fab fa-facebook-f"></i></a></li>
                        <li><a href="#" class="text-white"><i class="fab fa-twitter"></i></a></li>
                        <li><a href="#" class="text-white"><i class="fab fa-linkedin"></i></a></li>
                        <li><a href="#" class="text-white"><i class="fab fa-instagram"></i></a></li>
                    </ul>
                </div>
            </div>
            <div class="row mt-4">
                <div class="col-md-12 text-center">
                    <p class="mb-0">&copy; 2024 Gate. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
        function confirmDelete() {
            if (confirm("Êtes-vous sûr de vouloir supprimer votre compte ? Cette action est irréversible.")) {
                document.querySelector('form[action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"]').submit();
            }
        }

        (() => {
            'use strict';

            const forms = document.querySelectorAll('.needs-validation');

            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }

                    form.classList.add('was-validated');
                }, false);
            });
        })();
    </script>
</body>
</html>