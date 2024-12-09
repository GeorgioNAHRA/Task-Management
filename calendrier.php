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

// Gestion des requêtes AJAX uniquement
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $startDate = $_GET['start'] ?? null;
        $endDate = $_GET['end'] ?? null;

        $sql = "SELECT IDTache as id, Titre as title, datedebut as start, datefin as end FROM Tache";
        if ($startDate && $endDate) {
            $sql .= " WHERE STR_TO_DATE(datedebut, '%Y-%m-%d') >= '$startDate' AND STR_TO_DATE(datefin, '%Y-%m-%d') <= '$endDate'";
        }

        $result = mysqli_query($connection, $sql);

        if ($result) {
            $events = [];
            while ($row = mysqli_fetch_assoc($result)) {
                $events[] = [
                    'id' => $row['id'],
                    'title' => $row['title'],
                    'start' => $row['start'],
                    'end' => $row['end']
                ];
            }
            header('Content-Type: application/json');
            echo json_encode($events);
        } else {
            header('Content-Type: application/json');
            echo json_encode(["error" => "Erreur SQL : " . mysqli_error($connection), "query" => $sql]);
        }
        exit();
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

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
                    day: 'Jour',
                    list: 'Liste'
                },
                events: function(fetchInfo, successCallback, failureCallback) {
                    $.ajax({
                        url: 'calendrier.php',
                        type: 'GET',
                        data: {
                            start: fetchInfo.startStr,
                            end: fetchInfo.endStr
                        },
                        success: function(data) {
                            successCallback(data);
                        },
                        error: function(xhr) {
                            console.error("Erreur lors de la récupération des événements :", xhr.responseText);
                            failureCallback([]);
                        }
                    });
                }
            });
            calendar.render();
        });

        // Script pour les trois barres du sidebar
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".sidebarBtn");
        sidebarBtn.onclick = function () {
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