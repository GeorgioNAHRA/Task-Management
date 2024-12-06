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
        <div class="home-content">
            <div class="overview-boxes">
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Projet</div>
                        <div class="number">40,876</div>
                        <div class="indicator">
                            <i class="bx bx-up-arrow-alt"></i>
                            <span class="text">Depuis hier</span>
                        </div>
                    </div>
                    <i class="bx bx-cart-alt cart"></i>
                </div>
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Vente</div>
                        <div class="number">38,876</div>
                        <div class="indicator">
                            <i class="bx bx-up-arrow-alt"></i>
                            <span class="text">Depuis hier</span>
                        </div>
                    </div>
                    <i class="bx bxs-cart-add cart two"></i>
                </div>
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Profit</div>
                        <div class="number">12,876 F</div>
                        <div class="indicator">
                            <i class="bx bx-up-arrow-alt"></i>
                            <span class="text">Depuis hier</span>
                        </div>
                    </div>
                    <i class="bx bx-cart cart three"></i>
                </div>
                <div class="box">
                    <div class="right-side">
                        <div class="box-topic">Revenu</div>
                        <div class="number">11,086</div>
                        <div class="indicator">
                            <i class="bx bx-down-arrow-alt down"></i>
                            <span class="text">Aujourd'hui</span>
                        </div>
                    </div>
                    <i class="bx bxs-cart-download cart four"></i>
                </div>
            </div>

            <div class="sales-boxes">
                <div class="recent-sales box">
                    <div class="title">Projets récents</div>
                    <div class="sales-details">
                        <ul class="details">
                            <li class="topic">Date</li>
                            <li><a href="#">02 Jan 2021</a></li>
                            <li><a href="#">02 Jan 2021</a></li>
                            <li><a href="#">02 Jan 2021</a></li>
                            <li><a href="#">02 Jan 2021</a></li>
                            <li><a href="#">02 Jan 2021</a></li>
                            <li><a href="#">02 Jan 2021</a></li>
                            <li><a href="#">02 Jan 2021</a></li>
                        </ul>
                        <ul class="details">
                            <li class="topic">Client</li>
                            <li><a href="#">Abdoul Razak</a></li>
                            <li><a href="#">Abdel Nasser</a></li>
                            <li><a href="#">Maman Sani</a></li>
                            <li><a href="#">Narouwa</a></li>
                            <li><a href="#">Ishaka</a></li>
                            <li><a href="#">Abdoullah</a></li>
                            <li><a href="#">Adam</a></li>
                            <li><a href="#">Komche</a></li>
                            <li><a href="#">Adamou</a></li>
                        </ul>
                        <ul class="details">
                            <li class="topic">Projet</li>
                            <li><a href="#">Ordinateur</a></li>
                            <li><a href="#">iPhone</a></li>
                            <li><a href="#">Returned</a></li>
                            <li><a href="#">Ordinateur</a></li>
                            <li><a href="#">iPhone</a></li>
                            <li><a href="#">Returned</a></li>
                            <li><a href="#">Ordinateur</a></li>
                            <li><a href="#">iPhone</a></li>
                            <li><a href="#">Ordinateur</a></li>
                        </ul>
                        <ul class="details">
                            <li class="topic">Prix</li>
                            <li><a href="#">204.98 F</a></li>
                            <li><a href="#">24.55 F</a></li>
                            <li><a href="#">25.88 F</a></li>
                            <li><a href="#">170.66 F</a></li>
                            <li><a href="#">56.56 F</a></li>
                            <li><a href="#">44.95 F</a></li>
                            <li><a href="#">67.33 F</a></li>
                            <li><a href="#">23.53 F</a></li>
                            <li><a href="#">46.52 F</a></li>
                        </ul>
                    </div>
                    <div class="button">
                        <a href="#">Voir Tout</a>
                    </div>
                </div>
                <div class="top-sales box">
                    <div class="title">Produit le plus vendu</div>
                    <ul class="top-sales-details">
                        <li>
                            <a href="#">
                                <span class="product">Ordinateur</span>
                            </a>
                            <span class="price">1107 F</span>
                        </li>
                        <li>
                            <a href="#">
                                <span class="product">PC</span>
                            </a>
                            <span class="price">1567 F</span>
                        </li>
                        <li>
                            <a href="#">
                                <span class="product">Chaussure</span>
                            </a>
                            <span class="price">1234 F</span>
                        </li>
                        <li>
                            <a href="#">
                                <span class="product">Pantalon</span>
                            </a>
                            <span class="price">2312 F</span>
                        </li>
                        <li>
                            <a href="#">
                                <span class="product">Samsung</span>
                            </a>
                            <span class="price">1456 F</span>
                        </li>
                        <li>
                            <a href="#">
                                <span class="product">iPhone</span>
                            </a>
                            <span class="price">2345 F</span>
                        </li>
                        <li>
                            <a href="#">
                                <span class="product">iPhone X</span>
                            </a>
                            <span class="price">2345 F</span>
                        </li>
                        <li>
                            <a href="#">
                                <span class="product">TShirt</span>
                            </a>
                            <span class="price">1245 F</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <script>
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".sidebarBtn");
        sidebarBtn.onclick = function () {
            sidebar.classList.toggle("active");
            if (sidebar.classList.contains("active")) {
                sidebarBtn.classList.replace("bx-menu", "bx-menu-alt-right");
            } else sidebarBtn.classList.replace("bx-menu-alt-right", "bx-menu");
        };
    </script>
</body>
</html>