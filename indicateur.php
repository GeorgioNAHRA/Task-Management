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
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="dashboard.css" />
    <!-- Boxicons CDN Link -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <?php include('sidebar.php'); ?>
    <section class="home-section">
    <?php include('header_gestion.php'); ?>
        <!-- Section pour afficher les indicateurs clés -->
        <div class="key-indicators">
            <div>
                <h2>Nombre total de projets en cours</h2>
                <p id="totalProjects">0</p>
            </div>
            <div>
                <h2>Taux d’avancement moyen</h2>
                <p id="averageProgress">0%</            </div>
            <div>
                <h2>Dépassements budgétaires cumulés</h2>
                <p id="cumulativeBudgetExceedances">0</p>
            </div>
            <div>
                <h2>Retards accumulés sur les jalons</h2>
                <p id="accumulatedDelays">0</p>
            </div>
        </div>
    </section>

    <style>
        /* CSS pour les indicateurs clés */
        .key-indicators {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-top: 0;
            padding: 100px;
        }

        .key-indicators div {
            background-color: #f4f4f4;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .key-indicators h2 {
            font-size: 18px;
            margin-bottom: 10px;
        }

        .key-indicators p {
            font-size: 24px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
    </style>

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
</body>
</html>