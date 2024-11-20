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
                <a href="plan.php">
                    <i class="bx bx-list-ul"></i>
                    <span class="links_name">Plan</span>
                </a>
            </li>
            <li>
                <a href="indicateur.php" class="active">
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
                <span class="dashboard">Indicateur clés</span>
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