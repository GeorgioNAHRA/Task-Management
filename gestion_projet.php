<?php
session_start();
include('db.php');

// Vérification si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Erreur : Vous devez être connecté pour accéder à cette page.";
    exit();
}

// Informations de l'utilisateur connecté
$user_info = [
    'Prenom' => $_SESSION['prenom'],
    'Nom' => $_SESSION['nom'],
    'photo' => $_SESSION['photo'],
    'statu' => $_SESSION['statu']
];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "MNB_data";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fonction pour afficher les projets
function afficherProjets($conn, $user_info) {
    if ($user_info['statu'] === 'Admin') {
        $sql = "SELECT * FROM Projet";
    } else {
        $user_id = $_SESSION['user_id'];
        $sql = "SELECT * FROM Projet WHERE FIND_IN_SET('$user_id', IDUsers)";
    }

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row["IDProjet"] . '</td>';
            echo '<td>' . htmlspecialchars($row["nomProjet"]) . '</td>';
            echo '<td>' . htmlspecialchars($row["descriptionProjet"]) . '</td>';
            echo '<td>' . htmlspecialchars($row["Duree_projet"]) . '</td>';
            echo '<td>' . htmlspecialchars($row["Statu"]) . '</td>';
            echo '<td>' . htmlspecialchars($row["budget"]) . '</td>';
            echo '<td>';
            echo '<form method="post" action="plan.php" style="display:inline-block;">
                    <input type="hidden" name="id_projet" value="' . $row["IDProjet"] . '">
                    <button type="submit" class="btn btn-info">Gérer</button>
                  </form>';
            echo '</td>';
            echo '<td>';
            // Afficher les boutons "Modifier" et "Supprimer" pour tous les utilisateurs
            echo '<form method="get" action="modifier_projet.php" style="display:inline-block; margin-right: 10px;">
                    <input type="hidden" name="id" value="' . $row["IDProjet"] . '">
                    <button type="submit" class="btn btn-warning">Modifier</button>
                  </form>';
            echo '<form method="post" action="" style="display:inline-block;">
                    <input type="hidden" name="projet_id" value="' . $row["IDProjet"] . '">
                    <button type="submit" name="supprimer_projet" class="btn btn-danger">Supprimer</button>
                  </form>';
            echo '</td>';
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='8'>Aucun projet à afficher.</td></tr>";
    }
}

// Supprimer un projet
if (isset($_POST['supprimer_projet'])) {
    $projet_id = $_POST['projet_id'];
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
    <title>Gestion des Projets</title>
    <link rel="stylesheet" href="dashboard.css" />
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <?php include('sidebar.php'); ?>
    <section class="home-section">
        <?php include('header_gestion.php'); ?>
        <div class="home-content">
            <div class="projects-table">
                <h2>Projets</h2>
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
                        <?php afficherProjets($conn, $user_info); ?>
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
        .home-content {
            padding: 20px;
        }

        .projects-table {
            background: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .projects-table h2 {
            margin-bottom: 20px;
            font-size: 22px;
            font-weight: 500;
            color: #333;
        }

        .projects-table table {
            width: 100%;
            border-collapse: collapse;
        }

        .projects-table table thead tr {
            background: #2B3A42;
            color: #fff;
            text-align: left;
        }

        .projects-table table th,
        .projects-table table td {
            padding: 12px 15px;
            border-bottom: 1px solid #ddd;
        }

        .projects-table table tbody tr:hover {
            background: #f1f1f1;
        }

        .projects-table button {
            background: #2B3A42;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s ease;
        }

        .projects-table button:hover {
            background: #0e98e6;
        }
    </style>
</body>
</html>

<?php
$conn->close();
?>