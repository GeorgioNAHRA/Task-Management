<?php
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
        // Récupérer les paramètres de date
        $startDate = $_GET['start'] ?? null;
        $endDate = $_GET['end'] ?? null;

        // Requête pour récupérer les tâches (colonnes au format VARCHAR(50))
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
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier Interactif</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            margin-top: 20px;
        }

        .calendar-container {
            background-color: #ffffff;
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .fc-toolbar-title {
            font-size: 24px;
            color: #6c63ff;
            font-weight: bold;
            text-transform: uppercase;
            text-align: center;
        }

        .fc-button {
            background-color: #6c63ff;
            color: #fff;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            font-size: 14px;
        }

        .fc-button:hover {
            background-color: #5548c8;
        }

        .fc-day-today {
            background-color: #e8f7ff; /* Couleur de fond pour le jour actuel */
            border: none;
            border-radius: 0; /* Transforme en rectangle */
        }

        .fc-daygrid-day:hover {
            background-color: #f0f0f0;
            border-radius: 0; /* Supprime les coins arrondis */
        }

        .fc-daygrid-day {
            text-align: center;
            font-size: 14px;
            padding: 10px;
            transition: background-color 0.3s ease;
            border-radius: 0; /* Supprime les coins arrondis */
        }

        .fc-event {
            background-color: #6c63ff;
            color: #fff;
            border-radius: 5px; /* Arrondi léger pour les événements */
        }
    </style>
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/fr.js"></script>
</head>
<body>
    <div class="container mt-4">
        <div class="calendar-container">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- FullCalendar -->
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
    </script>
</body>
</html>