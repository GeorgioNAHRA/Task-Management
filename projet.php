<?php
session_start();
include('db.php');

// Vérification de l'accès
if (!isset($_SESSION['user_id'])) {
    echo "Erreur : Vous devez être connecté pour accéder à cette page.";
    exit();
}

// Informations de l'utilisateur connecté
$user_info = [
    'Prenom' => $_SESSION['prenom'],
    'Nom' => $_SESSION['nom'],
    'photo' => $_SESSION['photo'],
    'statu' => $_SESSION['statu'],
    'id_user' => $_SESSION['user_id']
];

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "MNB_data";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Création du projet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statu = $_POST['statu'] ?? 'En cours'; // Récupérer le statut ou utiliser "En cours" par défaut
    $nomProjet = $_POST['nom_projet'];
    $descriptionProjet = $_POST['description_projet'];
    $budget = $_POST['budget'];
    $duree = $_POST['duree_projet'];
    $participants = isset($_POST['participants']) ? $_POST['participants'] : [];

    // Ajouter automatiquement l'utilisateur connecté s'il est User
    if ($_SESSION['statu'] === 'User' && !in_array($_SESSION['user_id'], $participants)) {
        $participants[] = $_SESSION['user_id'];
    }

    $idUsers = implode(',', $participants); // Transformer les ID utilisateurs en chaîne (ex : "2,3,4")

    // Insérer le projet avec les utilisateurs associés
    $stmt = $conn->prepare("INSERT INTO Projet (nomProjet, descriptionProjet, Duree_projet, Statu, budget, IDUsers) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisis", $nomProjet, $descriptionProjet, $duree, $statu, $budget, $idUsers);
    $stmt->execute();

    echo "<p>Projet créé avec succès.</p>";
}
?>

<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8" />
    <title>Page projet</title>
    <link rel="stylesheet" href="dashboard.css" />
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Boxicons CDN Link -->
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
</head>
<body>
    <?php include('sidebar.php'); ?>
    <section class="home-section">
    <?php include('header_gestion.php'); ?>
        <div class="home-content">
            <!-- Project form -->
            <div class="container mt-4">
                <h2>Création de Projet</h2>
                <form class="needs-validation" novalidate method="post" action="">
                    <div class="form-group">
                        <label for="nom_projet">Nom du Projet :</label>
                        <input type="text" class="form-control" id="nom_projet" name="nom_projet" placeholder="Entrez le nom du projet" required>
                        <div class="invalid-feedback">Veuillez entrer le nom du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="description_projet">Description du Projet :</label>
                        <textarea class="form-control" id="description_projet" name="description_projet" placeholder="Entrez la description du projet" required></textarea>
                        <div class="invalid-feedback">Veuillez entrer la description du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="budget">Budget du Projet (en €) :</label>
                        <input type="number" class="form-control" id="budget" name="budget" placeholder="Entrez le budget du projet" required>
                        <div class="invalid-feedback">Veuillez entrer le budget du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="duree_projet">Durée du Projet (en jours) :</label>
                        <input type="number" class="form-control" id="duree_projet" name="duree_projet" placeholder="Entrez la durée du projet" required>
                        <div class="invalid-feedback">Veuillez entrer la durée du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="statu">Statut :</label>
                        <select class="form-control" id="statu" name="statu">
                            <option value="En cours" selected>En cours</option>
                            <option value="Terminé">Terminé</option>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner le statut du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="participants">Participants :</label>
                        <div id="participants">
                            <?php
                            $users = $conn->query('SELECT IDUser, nom FROM Utilisateur');
                            while ($user = $users->fetch_assoc()) {
                                $isCurrentUser = $user['IDUser'] == $_SESSION['user_id'];
                                $checked = $isCurrentUser ? 'checked' : '';
                                $disabled = ($_SESSION['statu'] === 'User' && $isCurrentUser) ? 'disabled' : '';
                                echo '<div class="participant-option">';
                                echo '<input type="checkbox" id="user_' . $user['IDUser'] . '" name="participants[]" value="' . $user['IDUser'] . '" ' . $checked . ' ' . $disabled . '>';
                                echo '<label for="user_' . $user['IDUser'] . '">' . $user['nom'] . ($isCurrentUser ? ' (Vous)' : '') . '</label>';
                                echo '</div>';
                            }
                            ?>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Soumettre</button>
                </form>
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