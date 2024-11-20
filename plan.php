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

// Ajouter une tâche
if (isset($_POST['add_task'])) {
    $taskName = $_POST['new-task'];
    $taskDeadline = $_POST['new-task-deadline'];
    $projectId = isset($_POST['IDProjet']) ? $_POST['IDProjet'] : null;
    $userId = isset($_POST['IDUser']) ? $_POST['IDUser'] : null;

    if ($projectId && $userId) {
        $sql = "INSERT INTO Tache (Titre, datefin, IDProjet_avoir, IDUser) VALUES ('$taskName', '$taskDeadline', '$projectId', '$userId')";
        if ($conn->query($sql) === TRUE) {
            echo "<p>Tâche ajoutée avec succès.</p>";
        } else {
            echo "<p>Erreur lors de l'ajout de la tâche: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Erreur: Aucun projet ou utilisateur sélectionné.</p>";
    }
}

// Supprimer une tâche
if (isset($_POST['delete_task'])) {
    $taskId = $_POST['task_id'];

    $sql = "DELETE FROM faire WHERE IDTache='$taskId'";
    if ($conn->query($sql) === TRUE) {
        $sql = "DELETE FROM commentaire WHERE IDTache_contenir2='$taskId'";
        if ($conn->query($sql) === TRUE) {
            $sql = "DELETE FROM Tache WHERE IDTache='$taskId'";
            if ($conn->query($sql) === TRUE) {
                echo "<p>Tâche supprimée avec succès.</p>";
            } else {
                echo "<p>Erreur lors de la suppression de la tâche: " . $conn->error . "</p>";
            }
        } else {
            echo "<p>Erreur lors de la suppression des commentaires: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Erreur lors de la suppression des enregistrements dans faire: " . $conn->error . "</p>";
    }
}

// Ajouter un participant
if (isset($_POST['add_participant'])) {
    $participantName = $_POST['new-participant-name'];
    $participantRole = $_POST['new-participant-role'];
    $equipeId = $_POST['equipe-id'];

    $sql = "SELECT IDUser FROM Utilisateur WHERE Nom='$participantName'";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $userId = $row['IDUser'];
        $sql = "INSERT INTO membreequipe (IDUser, roles, IDequipe) VALUES ('$userId', '$participantRole', '$equipeId')";
        if ($conn->query($sql) === TRUE) {
            echo "<p>Participant ajouté avec succès.</p>";
        } else {
            echo "<p>Erreur lors de l'ajout du participant: " . $conn->error . "</p>";
        }
    } else {
        echo "<p>Utilisateur non trouvé.</p>";
    }
}

// Ajouter une équipe
if (isset($_POST['add_team'])) {
    $teamName = $_POST['new-team-name'];

    $sql = "INSERT INTO equipe (roles, IDUser) VALUES ('$teamName', 1)";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Équipe créée avec succès.</p>";
    } else {
        echo "<p>Erreur lors de la création de l'équipe: " . $conn->error . "</p>";
    }
}

// Fonctions pour afficher les listes
function afficherTaches($conn) {
    $sql = "SELECT * FROM Tache";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="task-header"><span>Tâche</span><span>Date Limite</span><span>Actions</span></div>';
        while($row = $result->fetch_assoc()) {
            echo '<div class="task">';
            echo '<span class="task-name">' . $row["Titre"] . '</span>';
            echo '<span class="task-deadline">' . $row["datefin"] . '</span>';
            echo '<form method="post" action="" style="display:inline;">';
            echo '<input type="hidden" name="task_id" value="' . $row["IDTache"] . '">';
            echo '<button type="submit" name="delete_task">Supprimer</button>';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo "<p>Aucune tâche à afficher.</p>";
    }
}

function afficherParticipants($conn) {
    $sql = "SELECT U.Nom, U.Prenom, M.roles FROM Utilisateur U INNER JOIN membreequipe M ON U.IDUser = M.IDUser";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="participant-header"><span>Nom</span><span>Rôle</span></div>';
        while($row = $result->fetch_assoc()) {
            echo '<div class="participant">';
            echo '<span class="participant-name">' . $row["Nom"] . ' ' . $row["Prenom"] . '</span>';
            echo '<span class="participant-role">' . $row["roles"] . '</span>';
            echo '</div>';
        }
    } else {
        echo "<p>Aucun participant à afficher.</p>";
    }
}

function afficherEquipes($conn) {
    $sql = "SELECT * FROM equipe";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="team-header"><span>Équipe</span><span>Actions</span></div>';
        while($row = $result->fetch_assoc()) {
            echo '<div class="team">';
            echo '<span class="team-name">' . $row["roles"] . '</span>';
            echo '<form method="post" action="" style="display:inline;">';
            echo '<input type="hidden" name="team_id" value="' . $row["Idequipe"] . '">';
            echo '<button type="submit" name="delete_team">Supprimer</button>';
            echo '</form>';
            echo '</div>';
        }
    } else {
        echo "<p>Aucune équipe à afficher.</p>";
    }
}

// Supprimer une équipe
if (isset($_POST['delete_team'])) {
    $teamId = $_POST['team_id'];
    $sql = "DELETE FROM equipe WHERE Idequipe='$teamId'";
    if ($conn->query($sql) === TRUE) {
        echo "<p>Équipe supprimée avec succès.</p>";
    } else {
        echo "<p>Erreur lors de la suppression de l'équipe: " . $conn->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css" />
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
                <a href="plan.php" class="active">
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
            <div class="sidebar-button">
                <i class="bx bx-menu sidebarBtn"></i>
                <span class="dashboard">Plan</span>
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
        <div class="project-management">
            <h2>Gestion de Projet</h2>
            <div class="task-list">
                <?php afficherTaches($conn); ?>
            </div>
            <div class="task-actions">
                <form method="post" action="">
                    <input type="text" id="new-task" name="new-task" placeholder="Nouvelle tâche..." />
                    <input type="date" id="new-task-deadline" name="new-task-deadline" />
                    <select name="IDProjet">
                        <?php
                        $sql = "SELECT IDProjet, nomProjet FROM Projet";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row["IDProjet"] . '">' . $row["nomProjet"] . '</option>';
                        }
                        ?>
                    </select>
                    <select name="IDUser">
                        <?php
                        $sql = "SELECT IDUser, Nom FROM Utilisateur";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row["IDUser"] . '">' . $row["Nom"] . '</option>';
                        }
                        ?>
                    </select>
                    <button type="submit" name="add_task">Ajouter Tâche</button>
                </form>
            </div>
        </div>
        <div class="project-participants">
            <h2>Participants du Projet</h2>
            <div class="participant-list">
                <?php afficherParticipants($conn); ?>
            </div>
            <div class="participant-actions">
                <form method="post" action="">
                    <input type="text" id="new-participant-name" name="new-participant-name" placeholder="Nom du participant..." />
                    <input type="text" id="new-participant-role" name="new-participant-role" placeholder="Rôle du participant..." />
                    <select name="equipe-id">
                        <?php
                        $sql = "SELECT Idequipe, roles FROM equipe";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo '<option value="' . $row["Idequipe"] . '">' . $row["roles"] . '</option>';
                        }
                        ?>
                    </select>
                    <button type="submit" name="add_participant">Ajouter Participant</button>
                </form>
            </div>
        </div>
        <div class="project-teams">
            <h2>Création et Gestion des Équipes</h2>
            <div class="team-list">
                <?php afficherEquipes($conn); ?>
            </div>
            <div class="team-actions">
                <form method="post" action="">
                    <input type="text" id="new-team-name" name="new-team-name" placeholder="Nom de l'équipe..." />
                    <button type="submit" name="add_team">Créer Équipe</button>
                </form>
            </div>
        </div>
    </section>
    <style>
        .project-management, .project-participants, .project-teams {
            padding: 80px;
            margin-bottom: 10px;
        }
        .task-list, .participant-list, .team-list {
            margin-bottom: 20px;
        }
        .task-header, .participant-header, .team-header {
            display: flex;
            justify-content: space-between;
            font-weight: bold;
            padding: 10px;
            border-bottom: 1px solid #ccc;
        }
        .task, .participant, .team {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px;
            border: 1px solid #ccc;
            margin-bottom: 5px;
        }
        .task .task-name.done {
            text-decoration: line-through;
        }
        .task-actions, .participant-actions, .team-actions {
            display: flex;
            gap: 10px;
        }
        .task-actions input, .participant-actions input, .team-actions input, .task-actions button, .participant-actions button, .team-actions button {
            padding: 10px;
            font-size: 16px;
        }
    </style>
</body>
</html>
<?php
$conn->close();
?>