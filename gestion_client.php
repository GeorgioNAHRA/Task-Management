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

// Fonction pour afficher les utilisateurs
function afficherUtilisateurs($conn) {
    $sql = "SELECT * FROM Utilisateur WHERE Statu='en attente' OR Statu='accepter'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row["IDUser"] . '</td>';
            echo '<td>' . $row["Nom"] . '</td>';
            echo '<td>' . $row["Prenom"] . '</td>';
            echo '<td>';
            if ($row["Statu"] == 'en attente') {
                echo '<form method="post" action="" style="display:inline-block;"><input type="hidden" name="user_id" value="' . $row["IDUser"] . '"><button type="submit" name="accept" class="btn btn-primary">Accepter</button></form> ';
            }
            echo '<form method="post" action="" style="display:inline-block;"><input type="hidden" name="user_id" value="' . $row["IDUser"] . '"><button type="submit" name="delete" class="btn btn-danger">Supprimer</button></form>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='4'>Aucun utilisateur à afficher.</td></tr>";
    }
}

// Fonction pour afficher les projets en cours
function afficherProjetsEnCours($conn) {
    $sql = "SELECT * FROM Projet";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row["IDProjet"] . '</td>';
            echo '<td>' . $row["nomProjet"] . '</td>';
            echo '<td>' . $row["descriptionProjet"] . '</td>';
            echo '<td>' . $row["Duree_projet"] . '</td>';
            echo '<td>' . $row["Statu"] . '</td>';
            echo '<td>' . $row["budjet"] . '</td>';
            echo '<td>';
            echo '<form method="post" action="projet.php" style="display:inline-block;"><input type="hidden" name="projet_id" value="' . $row["IDProjet"] . '"><button type="submit" class="btn btn-info">Gérer</button></form>';
            echo '</td>';
            echo '<td>';
            echo '<form method="post" action="" style="display:inline-block;"><input type="hidden" name="projet_id" value="' . $row["IDProjet"] . '"><button type="submit" name="modifier_projet" class="btn btn-warning">Modifier</button></form> ';
            echo '<form method="post" action="" style="display:inline-block;"><input type="hidden" name="projet_id" value="' . $row["IDProjet"] . '"><button type="submit" name="supprimer_projet" class="btn btn-danger">Supprimer</button></form>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='8'>Aucun projet en cours à afficher.</td></tr>";
    }
}

// Accepter un compte utilisateur
if (isset($_POST['accept'])) {
    $user_id = $_POST['user_id'];
    $sql = "UPDATE Utilisateur SET Statu='accepter' WHERE IDUser='$user_id'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Compte utilisateur accepté avec succès.</p>";
    } else {
        echo "<p>Erreur lors de l'acceptation du compte: " . $conn->error . "</p>";
    }
}

// Supprimer un compte utilisateur
if (isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];
    
    // Supprimer d'abord les enregistrements dépendants dans la table 'creer'
    $sql = "DELETE FROM creer WHERE IDUser='$user_id'";
    $result = $conn->query($sql);

    if ($result === TRUE) {
        // Ensuite, supprimer l'utilisateur de la table 'Utilisateur'
        $sql = "DELETE FROM Utilisateur WHERE IDUser='$user_id'";
        if ($conn->query($sql) === TRUE) {
            echo "<p>Compte utilisateur et les données associées supprimés avec succès.</p>";
        } else {
            echo "<p>Erreur lors de la suppression du compte utilisateur: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Erreur lors de la suppression des données associées: " . $conn->error . "</p>";
    }
}

// Modifier un projet
if (isset($_POST['modifier_projet'])) {
    $projet_id = $_POST['projet_id'];
    $sql = "SELECT * FROM Projet WHERE IDProjet='$projet_id'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo '<h2>Modifier le Projet</h2>';
        echo '<form method="post" action="">';
        echo '<input type="hidden" name="projet_id" value="' . $row["IDProjet"] . '">';
        echo '<label for="nom_projet">Nom du Projet:</label>';
        echo '<input type="text" id="nom_projet" name="nom_projet" value="' . $row["nomProjet"] . '" required>';
        echo '<br>';
        echo '<label for="description">Description:</label>';
        echo '<textarea id="description" name="description" required>' . $row["descriptionProjet"] . '</textarea>';
        echo '<br>';
        echo '<label for="duree_projet">Durée du Projet:</label>';
        echo '<input type="text" id="duree_projet" name="duree_projet" value="' . $row["Duree_projet"] . '" required>';
        echo '<br>';
        echo '<label for="statu">Statut:</label>';
        echo '<input type="text" id="statu" name="statu" value="' . $row["Statu"] . '" required>';
        echo '<br>';
        echo '<label for="budjet">Budget:</label>';
        echo '<input type="number" id="budjet" name="budjet" value="' . $row["budjet"] . '" required>';
        echo '<br>';
        echo '<button type="submit" name="sauvegarder_projet">Sauvegarder</button>';
        echo '</form>';
    }
}

// Sauvegarder les modifications d'un projet
if (isset($_POST['sauvegarder_projet'])) {
    $projet_id = $_POST['projet_id'];
    $nom_projet = $_POST['nom_projet'];
    $description = $_POST['description'];
    $duree_projet = $_POST['duree_projet'];
    $statu = $_POST['statu'];
    $budjet = $_POST['budjet'];

    $sql = "UPDATE Projet SET nomProjet='$nom_projet', Duree_projet='$duree_projet', descriptionProjet='$description', Statu='$statu', budjet='$budjet' WHERE IDProjet='$projet_id'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Projet modifié avec succès.</p>";
    } else {
        echo "<p>Erreur lors de la modification du projet: " . $conn->error . "</p>";
    }
}

// Supprimer un projet
if (isset($_POST['supprimer_projet'])) {
    $projet_id = $_POST['projet_id'];

    // Supprimer d'abord les enregistrements dépendants dans la table 'commentaire'
    $sql = "SELECT IDTache FROM Tache WHERE IDProjet_avoir='$projet_id'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $idtache = $row['IDTache'];
            $sql = "DELETE FROM commentaire WHERE IDTache_contenir2='$idtache'";
            if (!$conn->query($sql)) {
                echo "<p>Erreur lors de la suppression des commentaires de la tâche $idtache: " . $conn->error . "</p>";
                exit;
            }
        }
    }

    // Tables à supprimer avec la contrainte sur la colonne appropriée
    $tables = [
        'notifier' => 'IDProjet',
        'creer' => 'IDProjet',
        'dossier' => 'IDProjet__contenir1',
        'modifier' => 'IDProjet'
    ];

    foreach ($tables as $table => $column) {
        $sql = "DELETE FROM $table WHERE $column='$projet_id'";
        if (!$conn->query($sql)) {
            echo "<p>Erreur lors de la suppression des données associées dans la table '$table': " . $conn->error . "</p>";
            exit;
        }
    }

    // Enfin, supprimer le projet de la table 'Projet'
    $sql = "DELETE FROM Projet WHERE IDProjet='$projet_id'";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Projet supprimé avec succès.</p>";
    } else {
        echo "<p>Erreur lors de la suppression du projet: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css" />
    <!-- Boxicons CDN Link -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <div class="sidebar">
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
                <a href="projet.php">
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
                <a href="gestion_client.php" class="active">
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
            <div class="sidebar-button">
                <i class="bx bx-menu sidebarBtn"></i>
                <span class="dashboard">Gestion Client</span>
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
            <div class="users-table">
                <h2>Utilisateurs en attente de validation</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="pending-users">
                        <?php afficherUtilisateurs($conn); ?>
                    </tbody>
                </table>
            </div>

            <div class="users-table">
                <h2>Utilisateurs enregistrés</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Prénom</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="registered-users">
                        <?php afficherUtilisateurs($conn); ?>
                    </tbody>
                </table>
            </div>

            <div class="projects-table">
                <h2>Projets en cours</h2>
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nom</th>
                            <th>Description</th>
                            <th>Durée</th>
                            <th>Statut</th>
                            <th>Budget</th>
                            <th>Gérer</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody id="ongoing-projects">
                        <?php afficherProjetsEnCours($conn); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

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

    <style>
        /* Styles pour le contenu de la page */
        .home-content {
            padding: 20px;
        }

        .users-table,
        .projects-table {
            background: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .users-table h2,
        .projects-table h2 {
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: 500;
            color: #333;
        }

        .users-table table,
        .projects-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .users-table table thead tr,
        .projects-table table thead tr {
            background: #2B3A42;
            color: #fff;
            text-align: left;
        }

        .users-table table th,
        .users-table table td,
        .projects-table table th,
        .projects-table table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        .users-table table tbody tr:hover,
        .projects-table table tbody tr:hover {
            background: #f1f1f1;
        }

        .users-table button,
        .projects-table button {
            background: #2B3A42;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .users-table button:hover,
        .projects-table button:hover {
            background: #0e98e6;
        }
    </style>
</body>
</html>

<?php
$conn->close();
?>