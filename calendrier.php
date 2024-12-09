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
    <link rel="stylesheet" href="dashboard.css" />
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        .home-content {
            padding: 20px;
        }
        .calendar-container {
            background-color: #fff;
            padding: 20px;
            margin-top: 20px;
            border-radius: 10px;
            box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
        }
        .fc-toolbar-title {
            font-size: 22px;
            color: #6c63ff;
            text-transform: uppercase;
            text-align: center;
        }
        .fc-button {
            background-color: #6c63ff;
            color: #fff;
            border-radius: 5px;
            border: none;
        }
        .fc-button:hover {
            background-color: #5548c8;
        }
        .fc-day-today {
            background-color: #e8f7ff;
        }
        .fc-event {
            background-color: #6c63ff;
            color: #fff;
            border-radius: 5px;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }
        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            border-radius: 10px;
            width: 50%;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }
        .modal-header {
            font-size: 20px;
            font-weight: bold;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var tasks = <?php echo json_encode($taches); ?>;

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Aujourd\'hui',
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste'
                },
                events: tasks.map(task => ({
                    id: task.IDTache,
                    title: task.Titre,
                    start: task.datedebut,
                    end: task.datefin
                })),
                eventClick: function(info) {
                    var task = tasks.find(t => t.IDTache == info.event.id);
                    document.getElementById('taskTitle').innerText = task.Titre;
                    document.getElementById('taskDescription').innerText = task.description;
                    document.getElementById('taskStart').innerText = task.datedebut;
                    document.getElementById('taskEnd').innerText = task.datefin;
                    document.getElementById('taskUsers').innerText = task.IDUser || 'Aucun utilisateur';
                    document.getElementById('taskId').value = task.IDTache;

                    document.getElementById('taskModal').style.display = 'block';
                }
            });
            calendar.render();

            var modal = document.getElementById("taskModal");
            var closeModal = document.getElementsByClassName("close")[0];
            closeModal.onclick = function() {
                modal.style.display = "none";
            };
            window.onclick = function(event) {
                if (event.target == modal) {
                    modal.style.display = "none";
                }
            };
        });

        // Script pour gérer le sidebar
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".sidebarBtn");
        sidebarBtn.onclick = function() {
            sidebar.classList.toggle("active");
            if (sidebar.classList.contains("active")) {
                sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
            } else {
                sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
            }
        };
    </script>
</body>
</html>