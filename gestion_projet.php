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
    'statu' => $_SESSION['statu'],
    'id_user' => $_SESSION['user_id']
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

// Récupérer les alertes des tâches en retard ou à échéance
$current_date = date('Y-m-d');
$user_id = $_SESSION['user_id'];

// Requête SQL pour récupérer les tâches uniquement pour les projets auxquels l'utilisateur est affecté
$alert_taches = $conn->query("
    SELECT t.IDTache, t.Titre, t.description, t.datefin, p.nomProjet, u.Prenom, u.Nom
    FROM Tache t
    JOIN Projet p ON t.IDProjet = p.IDProjet
    LEFT JOIN Utilisateur u ON FIND_IN_SET(u.IDUser, t.IDUser)
    WHERE t.datefin <= '$current_date' 
    AND FIND_IN_SET('$user_id', p.IDUsers)
    ORDER BY t.datefin ASC
");

// Fonction pour afficher les projets
function afficherProjets($conn, $user_id) {
    $sql = "SELECT * FROM Projet WHERE FIND_IN_SET('$user_id', IDUsers)";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row["nomProjet"]) . '</td>';
            echo '<td>' . $row["IDProjet"] . '</td>';
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
            echo '</tr>';
        }
    } else {
        echo "<tr><td colspan='7'>Aucun projet à afficher.</td></tr>";
    }
}

// Suppression d'un projet (si nécessaire)
if (isset($_POST['supprimer_projet'])) {
    $projet_id = $_POST['projet_id'];
    $sql = "DELETE FROM Projet WHERE IDProjet='$projet_id'";
    if ($conn->query($sql) === TRUE) {
        echo "<script>alert('Projet supprimé avec succès.'); location.reload();</script>";
    } else {
        echo "<script>alert('Erreur lors de la suppression du projet : " . $conn->error . "');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Gestion des Projets</title>
    <link rel="stylesheet" href="dashboard.css" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <style>
        .home-content {
            padding: 20px;
        }

        /* Section des alertes */
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffeeba;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 30px;
        }

        .table-striped > tbody > tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .table thead th {
            text-align: left;
            font-weight: bold;
            background-color: #ffc107;
            color: #fff;
        }

        /* Section des projets */
        .projects-section {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            margin-top: 30px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }

        .projects-section h2 {
            color: #333;
            margin-bottom: 15px;
        }

        .projects-section table thead th {
            background-color: #f8f9fa;
            color: #333;
            border-bottom: 2px solid #ddd;
        }

        .projects-section table tbody tr td {
            color: #333;
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    <section class="home-section">
        <?php include('header_gestion.php'); ?>
        <div class="home-content">

            <!-- Section des alertes -->
            <div class="projects-table">
                <h2 class="text-warning">Alertes des Tâches</h2>
                <div class="alert alert-warning">
                    <h5>Tâches en retard ou à échéance</h5>
                    <?php if ($alert_taches->num_rows > 0): ?>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Projet</th>
                                    <th>Titre</th>
                                    <th>Description</th>
                                    <th>Assigné à</th>
                                    <th>Date d'échéance</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($tache = $alert_taches->fetch_assoc()): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($tache['nomProjet']) ?></td>
                                        <td><?= htmlspecialchars($tache['Titre']) ?></td>
                                        <td><?= htmlspecialchars($tache['description']) ?></td>
                                        <td><?= htmlspecialchars($tache['Prenom'] . ' ' . $tache['Nom']) ?></td>
                                        <td><?= htmlspecialchars(date('d/m/Y', strtotime($tache['datefin']))) ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>Aucune tâche en retard ou arrivant à échéance.</p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Section des projets associés -->
            <div class="projects-section">
                <h2>Projets Associés</h2>
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Nom du Projet</th>
                            <th>ID</th>
                            <th>Description</th>
                            <th>Durée</th>
                            <th>Statut</th>
                            <th>Budget</th>
                            <th>Gérer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php afficherProjets($conn, $user_id); ?>
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
        };
    </script>
</body>
</html>

<?php
$conn->close();
?>