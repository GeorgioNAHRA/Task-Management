<?php
session_start();
include('db.php');

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo "Erreur : Vous devez être connecté pour accéder à cette page.";
    exit();
}

// Récupérer les informations de l'utilisateur connecté
$user_info = [
    'Prenom' => $_SESSION['prenom'] ?? '',
    'Nom' => $_SESSION['nom'] ?? '',
    'photo' => $_SESSION['photo'] ?? 'default.png',
    'statu' => $_SESSION['statu'] ?? 'User'
];

// Connexion à la base de données
$hostname = "localhost";
$username = "root";
$password = "";
$database = "mnb_data";

$connection = mysqli_connect($hostname, $username, $password, $database);

if (!$connection) {
    die("Erreur de connexion à la base de données: " . mysqli_connect_error());
}

// Récupérer toutes les tâches
$taches = [];
$sql = "SELECT Tache.*, GROUP_CONCAT(Utilisateur.Prenom, ' ', Utilisateur.Nom) AS users 
        FROM Tache 
        LEFT JOIN Utilisateur ON FIND_IN_SET(Utilisateur.IDUser, Tache.IDUser)
        GROUP BY Tache.IDTache";

$result = mysqli_query($connection, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
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
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>
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

        /* Style de la fenêtre modale */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 50%;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.5);
        }

        .modal-header, .modal-body, .modal-footer {
            margin-bottom: 10px;
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover, .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <?php include('sidebar.php'); ?>

    <!-- Main content section -->
    <section class="home-section">
        <!-- Header -->
        <?php include('header_gestion.php'); ?>

        <!-- Main Content -->
        <div class="home-content">
            <div class="calendar-container">
                <div id="calendar"></div>
            </div>
        </div>
    </section>

    <!-- Fenêtre modale -->
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
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');
            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'fr', // Définit la langue en français
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                buttonText: {
                    today: 'Aujourd\'hui',
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour'
                },
                events: <?php echo json_encode(array_map(function($tache) {
                    return [
                        'id' => $tache['IDTache'],
                        'title' => $tache['Titre'],
                        'start' => $tache['datedebut'],
                        'end' => $tache['datefin']
                    ];
                }, $taches)); ?>,
                eventClick: function(info) {
                    // Afficher les détails de la tâche dans la fenêtre modale
                    var task = <?php echo json_encode($taches); ?>.find(t => t.IDTache == info.event.id);
                    document.getElementById('taskTitle').innerText = task.Titre;
                    document.getElementById('taskDescription').innerText = task.description;
                    document.getElementById('taskStart').innerText = task.datedebut;
                    document.getElementById('taskEnd').innerText = task.datefin;
                    document.getElementById('taskUsers').innerText = task.users || 'Aucun utilisateur';

                    // Afficher la fenêtre modale
                    document.getElementById('taskModal').style.display = 'block';
                }
            });
            calendar.render();

            // Gestion de la fermeture de la modale
            document.querySelector('.close').onclick = function() {
                document.getElementById('taskModal').style.display = 'none';
            };
        });
    </script>
</body>
</html>