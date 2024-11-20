<?php
session_start();
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                <div>
                    <button class="btn btn-outline-light me-2" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">Se connecter</button>
                    <form class="dropdown-menu p-4">
                        <div class="mb-3">
                            <label for="exampleDropdownFormEmail2" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="exampleDropdownFormEmail2" placeholder="email@example.com">
                        </div>
                        <div class="mb-3">
                            <label for="exampleDropdownFormPassword2" class="form-label">Password</label>
                            <input type="password" class="form-control" id="exampleDropdownFormPassword2" placeholder="Password">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="dropdownCheck2">
                                <label class="form-check-label" for="dropdownCheck2">Remember me</label>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary">Connecter</button>
                    </form>
                    <a href="signup.php" class="btn btn-light">S'inscrire</a>
                    <a href="user.php" class="btn btn-light">Essayer Gate</a>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="py-5">
        <?php if (isset($error_message)) : ?>
            <p style="color: red;"><?php echo $error_message; ?></p>
        <?php endif; ?>
        <form class="row g-3 needs-validation" novalidate method="post" action="" enctype="multipart/form-data">
            <div class="col-md-4">
                <label for="nom" class="form-label">Nom</label>
                <input type="text" class="form-control" id="nom" name="nom" required>
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>
            <div class="col-md-4">
                <label for="prenom" class="form-label">Prenom</label>
                <input type="text" class="form-control" id="prenom" name="prenom" required>
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>
            <div class="col-md-4">
                <label for="mail" class="form-label">E-mail address</label>
                <input type="email" class="form-control" id="mail" name="mail" required>
                <div class="invalid-feedback">
                    Veuillez fournir une adresse valide.
                </div>
            </div>
            <div class="col-md-4">
                <label for="photo" class="form-label">Photo de profil</label>
                <input type="file" class="form-control" id="photo" name="photo" accept="image/*">
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label">Mot de Passe</label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">
                    Veuillez choisir un bon mot de passe.
                </div>
            </div>
            <div class="col-md-6">
                <label for="password_confirm" class="form-label">Confirmer mot de Passe</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                <div class="invalid-feedback">
                    Le mot de passe ne correspondant pas.
                </div>
            </div>
            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
                    <label class="form-check-label" for="invalidCheck">
                        Agree to terms and conditions
                    </label>
                    <div class="invalid-feedback">
                        Vous devez accepter avant de continuer.
                    </div>
                </div>
            </div>
            <div class="col-12">
                <button type="submit" class="btn btn-primary">S'inscrire</button>
            </div>
        </form>
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
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (() => {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            const forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.from(forms).forEach(form => {
                form.addEventListener('submit', event => {
                    if (!form.checkValidity()) {
                        event.preventDefault()
                        event.stopPropagation()
                    }

                    form.classList.add('was-validated')
                }, false)
            })
        })()
    </script>
</body>
</html>