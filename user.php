<?php
session_start();
include('db.php');

// Check if the user is logged in and has the 'Admin' or 'Accepter' status
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['statu'], ['Admin', 'accepter'])) {
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

// Fonctions pour afficher les listes
function afficherTaches($conn) {
    $sql = "SELECT * FROM Tache";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        echo '<div class="task-header"><span>Tâche</span><span>Date Limite</span></div>';
        while($row = $result->fetch_assoc()) {
            echo '<div class="task">';
            echo '<span class="task-name">' . $row["Titre"] . '</span>';
            echo '<span class="task-deadline">' . $row["datefin"] . '</span>';
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
        echo '<div class="team-header"><span>Équipe</span></div>';
        while($row = $result->fetch_assoc()) {
            echo '<div class="team">';
            echo '<span class="team-name">' . $row["roles"] . '</span>';
            echo '</div>';
        }
    } else {
        echo "<p>Aucune équipe à afficher.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Client Panel</title>
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
                <a href="user.php" class="active">
                    <i class="bx bx-list-ul"></i>
                    <span class="links_name">Utilisateur</span>
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
        </div>
        <div class="project-participants">
            <h2>Participants du Projet</h2>
            <div class="participant-list">
                <?php afficherParticipants($conn); ?>
            </div>
        </div>
        <div class="project-teams">
            <h2>Création et Gestion des Équipes</h2>
            <div class="team-list">
                <?php afficherEquipes($conn); ?>
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
    </style>
</body>
</html>
<?php
$conn->close();
?>
