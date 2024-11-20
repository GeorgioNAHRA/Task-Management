<?php
session_start();
include('db.php');

// Check if the user is logged in and has the 'Admin' status
if (!isset($_SESSION['user_id']) || $_SESSION['statu'] !== 'Admin') {
    echo "Erreur : Vous n'êtes pas autorisé à accéder à cette page.";
    exit();
}

$user_info = [
    'Prenom' => $_SESSION['prenom'],
    'Nom' => $_SESSION['nom'],
    'photo' => $_SESSION['photo']
];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "gate_data";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Créer un projet
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['creer_projet'])) {
    $nom_projet = $_POST['nomProjet'];
    $description = $_POST['description'];
    $duree_projet = $_POST['duree'];
    $taches = $_POST['taches'];
    $statut = $_POST['statut'];
    $budget = $_POST['budget'];

    $sql = "INSERT INTO Projet (nomProjet, Duree_projet, descriptionProjet, tachesprojets, Statu, budjet) 
            VALUES ('$nom_projet', '$duree_projet', '$description', '$taches', '$statut', '$budget')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Projet créé avec succès.</p>";
    } else {
        echo "<p>Erreur lors de la création du projet: " . $conn->error . "</p>";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Page projet</title>
    <link rel="stylesheet" href="dashboard.css" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons CDN Link -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <div class="sidebar">
        <!-- Sidebar content -->
        <div class="logo-details">
            <a href="GATE.php" class="active">
                <i class="bx bxl-c-plus-plus"></i>
                <span class="logo_name">GATE</span>
            </a>
        </div>
        <ul class="nav-links">
            <li>
                <a href="dashboard.php">
                    <i class="bx bx-grid-alt"></i>
                    <span class="links_name">Dashboard</span>
                </a>
            </li>
            <li>
                <a href="projet.php" class="active">
                    <i class="bx bx-box"></i>
                    <span class="links_name">Projet</span>
                </a>
            </li>
            <li>
                <a href="plan.php">
                    <i class="bx bx-list-ul"></i>
                    <span class="links_name">Plan</span>
                </a>
            </li>
            <li>
                <a href="indicateur.php">
                    <i class="bx bx-pie-chart-alt-2"></i>
                    <span class="links_name">Indicateur clés</span>
                </a>
            </li>
            <li>
                <a href="gestion_client.php">
                    <i class="bx bx-grid-alt"></i>
                    <span class="links_name">Gestion Client</span>
                </a>
            </li>
            <li>
                <a href="calendrier.php">
                    <i class="bx bx-coin-stack"></i>
                    <span class="links_name">Calendrier</span>
                </a>
            </li>
            <li class="log_out">
                <a href="logout.php">
                    <i class="bx bx-log-out"></i>
                    <span class="links_name">Déconnexion</span>
                </a>
            </li>
        </ul>
    </div>
    <section class="home-section">
        <nav>
            <!-- Navigation content -->
            <div class="sidebar-button">
                <i class="bx bx-menu sidebarBtn"></i>
                <span class="dashboard">Projet</span>
            </div>
            <div class="search-box">
                <input type="text" placeholder="Recherche..." />
                <i class="bx bx-search"></i>
            </div>
            <div class="profile-details">
                <?php if (!empty($user_info['photo'])): ?>
                    <img src="pdp/<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                <?php endif; ?>
                <span class="admin_name"><?php echo htmlspecialchars($user_info['Prenom'] . ' ' . $user_info['Nom']); ?></span>
                <i class="bx bx-chevron-down"></i>
            </div>
        </nav>
        <div class="home-content">
            <!-- Project form -->
            <div class="container mt-4">
                <h2>Création de Projet</h2>
                <form class="needs-validation" novalidate method="post" action="">
                    <div class="form-group">
                        <label for="nomProjet">Nom du Projet:</label>
                        <input type="text" class="form-control" id="nomProjet" name="nomProjet" placeholder="Entrez le nom du projet" required>
                        <div class="invalid-feedback">Veuillez entrer le nom du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea class="form-control" id="description" name="description" placeholder="Entrez la description du projet" required></textarea>
                        <div class="invalid-feedback">Veuillez entrer la description du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="duree">Durée du Projet:</label>
                        <input type="text" class="form-control" id="duree" name="duree" placeholder="Entrez la durée du projet" required>
                        <div class="invalid-feedback">Veuillez entrer la durée du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="taches">Tâches du Projet:</label>
                        <textarea class="form-control" id="taches" name="taches" placeholder="Entrez les tâches du projet" required></textarea>
                        <div class="invalid-feedback">Veuillez entrer les tâches du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="statut">Statut:</label>
                        <select class="form-control" id="statut" name="statut" required>
                            <option value="">Sélectionnez le statut</option>
                            <option value="En cours">En cours</option>
                            <option value="Terminé">Terminé</option>
                            <option value="En attente">En attente</option>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner le statut du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="budget">Budget:</label>
                        <input type="text" class="form-control" id="budget" name="budget" placeholder="Entrez le budget du projet" required>
                        <div class="invalid-feedback">Veuillez entrer le budget du projet.</div>
                    </div>
                    <button type="submit" name="creer_projet" class="btn btn-primary">Soumettre</button>
                </form>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- Custom JavaScript -->
    <script>
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".sidebarBtn");
        sidebarBtn.onclick = function () {
            sidebar.classList.toggle("active");
            if (sidebar.classList.contains("active")) {
                sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
            } else sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        }
    </script>
    <!-- JavaScript pour la validation Bootstrap -->
    <script>
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>