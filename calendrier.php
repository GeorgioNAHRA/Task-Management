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
    'Prenom' => $_SESSION['prenom'] ?? '',
    'Nom' => $_SESSION['nom'] ?? '',
    'photo' => $_SESSION['photo'] ?? 'default.png',
    'statu' => $_SESSION['statu'] ?? 'User',
    'id_user' => $_SESSION['user_id']
];

$hostname = "localhost";
$username = "root";
$password = "";
$database = "mnb_data";

// Connexion à la base de données
$connection = mysqli_connect($hostname, $username, $password, $database);
if (!$connection) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}

// Charger les tâches depuis la base de données
$taches = [];
if ($user_info['statu'] === 'Admin') {
    // Administrateur : voir toutes les tâches
    $sql = "SELECT * FROM Tache";
} else {
    // Utilisateur : voir uniquement les tâches auxquelles il est affecté
    $user_id = $user_info['id_user'];
    $sql = "SELECT * FROM Tache WHERE FIND_IN_SET('$user_id', IDUser)";
}

$result = $connection->query($sql);
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $taches[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier - Gestion MNB</title>
    <link rel="stylesheet" href="css/dashboard.css" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/calendrier.css">
    <script type="text/javascript" src="js/sidebar.js"></script>
    <script>
        var tasks = <?php echo json_encode($taches); ?>;
    </script>
    <script src="js/calendrier.js"></script>
</head>
<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Main Content -->
    <section class="home-section">
        <!-- Header -->
        <?php include('header_gestion.php'); ?>

        <div class="home-content">
            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </section>

    <!-- Modal pour afficher les détails de la tâche -->
    <div id="taskModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <div class="modal-header">
                <h2 id="taskTitle">Titre de la tâche</h2>
            </div>
            <div class="modal-body">
                <p><strong>Description :</strong> <span id="taskDescription"></span></p>
                <p><strong>Date de début :</strong> <span id="taskStart"></span></p>
                <p><strong>Date de fin :</strong> <span id="taskEnd"></span></p>
                <p><strong>Utilisateurs associés :</strong> <span id="taskUsers"></span></p>
            </div>
            <div class="modal-footer">
                <form method="post" action="plan.php" id="manageTaskForm">
                    <input type="hidden" name="id_tache" id="taskId">
                    <button type="submit" class="btn btn-primary">Gérer</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</body>
</html>