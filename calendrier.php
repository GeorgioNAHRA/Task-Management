<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier de Projet Annuel</title>
    <link rel="stylesheet" href="calendrier.css">
</head>
<body>
    <div class="container">
        <div class="project-form">
            <h3>Ajouter un projet</h3>
            <form id="projectForm">
                <label for="projectName">Nom du projet:</label>
                <input type="text" id="projectName" required>

                <label for="startDate">Date de début:</label>
                <input type="date" id="startDate" required>

                <label for="endDate">Date de fin:</label>
                <input type="date" id="endDate" required>

                <button type="submit">Ajouter</button>
            </form>
        </div>
        <div class="calendar-container">
            <div class="calendar-navigation">
                <button id="prevYear">&laquo;</button>
                <button id="prevMonth">&lsaquo;</button>
                <span id="currentMonthYear"></span>
                <button id="nextMonth">&rsaquo;</button>
                <button id="nextYear">&raquo;</button>
            </div>
            <div class="calendar" id="calendar"></div>
        </div>
    </div>
    <script src="calendrier.js"></script>
</body>
</html>
