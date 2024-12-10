<div class="sidebar">
    <!-- Intégration du logo -->
    <div class="logo-details">
        <a href="MNB.php" class="active d-flex align-items-center">
            <img src="mnb.jpeg" alt="MNB Logo" style="width: 70px; height: 50px; object-fit: contain; position: relative; top: 15px; margin-right: 10px;">
            <span class="logo_name">MNB</span>
        </a>
    </div>
    <ul class="nav-links">
        <li>
            <a href="projet.php">
                <i class="bx bx-box"></i>
                <span class="links_name">Création Projet</span>
            </a>
        </li>
        <?php if ($_SESSION['statu'] === 'Admin'): ?>
            <li>
                <a href="gestion_client.php">
                    <i class="bx bx-user"></i>
                    <span class="links_name">Gestion Clients</span>
                </a>
            </li>
        <?php endif; ?>
        <li>
            <a href="gestion_projet.php">
                <i class="bx bx-task"></i>
                <span class="links_name">Gestion Projets</span>
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