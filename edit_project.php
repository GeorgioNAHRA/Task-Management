<?php
session_start();
include('db.php');

// Vérification de l'accès
if (!isset($_SESSION['user_id'])) {
    echo "Erreur : Vous devez être connecté pour accéder à cette page.";
    exit();
}

$user_info = [
    'Prenom' => $_SESSION['prenom'],
    'Nom' => $_SESSION['nom'],
    'photo' => $_SESSION['photo'],
    'statu' => $_SESSION['statu'],
    'id_user' => $_SESSION['user_id']
];

// Vérifier si un ID projet est fourni
if (!isset($_GET['id'])) {
    echo "Aucun projet sélectionné.";
    exit();
}

$projet_id = $_GET['id'];

// Récupération des informations du projet
$sql = "SELECT * FROM Projet WHERE IDProjet='$projet_id'";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Projet introuvable.";
    exit();
}

$row = $result->fetch_assoc();

// Vérification si l'utilisateur fait partie du projet (si non admin)
if ($user_info['statu'] !== 'Admin') {
    $project_users = explode(',', $row['IDUsers']);
    if (!in_array($user_info['id_user'], $project_users)) {
        echo "Erreur : Vous n'êtes pas autorisé à accéder à ce projet.";
        exit();
    }
}

// Sauvegarder les modifications du projet
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nomProjet = $_POST['nom_projet'];
    $descriptionProjet = $_POST['description_projet'];
    $duree = $_POST['duree_projet'];
    $budget = $_POST['budget'];
    $statu = $_POST['statu'];

    $sql = "UPDATE Projet SET 
                nomProjet='$nomProjet', 
                descriptionProjet='$descriptionProjet', 
                Duree_projet='$duree', 
                Statu='$statu', 
                budget='$budget' 
            WHERE IDProjet='$projet_id'";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p>Projet modifié avec succès.</p>";
    } else {
        echo "<p>Erreur lors de la modification du projet: " . $conn->error . "</p>";
    }
}
?>


<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Projet</title>
    <link rel="stylesheet" href="css/dashboard.css">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
    <script type="text/javascript" src="js/sidebar.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
    <?php include('sidebar.php'); ?>
    <section class="home-section">
    <?php include('header_management.php'); ?>
        <div class="home-content">
            <div class="container mt-4">
                <h2>Modifier le Projet</h2>
                <form class="needs-validation" novalidate method="post">
                    <div class="form-group">
                        <label for="nom_projet">Nom du Projet :</label>
                        <input type="text" class="form-control" id="nom_projet" name="nom_projet" value="<?php echo htmlspecialchars($row['nomProjet']); ?>" required>
                        <div class="invalid-feedback">Veuillez entrer le nom du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="description_projet">Description du Projet :</label>
                        <textarea class="form-control" id="description_projet" name="description_projet" required><?php echo htmlspecialchars($row['descriptionProjet']); ?></textarea>
                        <div class="invalid-feedback">Veuillez entrer la description du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="budget">Budget du Projet (en €) :</label>
                        <input type="number" class="form-control" id="budget" name="budget" value="<?php echo htmlspecialchars($row['budget']); ?>" required>
                        <div class="invalid-feedback">Veuillez entrer le budget du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="duree_projet">Durée du Projet (en jours) :</label>
                        <input type="number" class="form-control" id="duree_projet" name="duree_projet" value="<?php echo htmlspecialchars($row['Duree_projet']); ?>" required>
                        <div class="invalid-feedback">Veuillez entrer la durée du projet.</div>
                    </div>
                    <div class="form-group">
                        <label for="statu">Statut :</label>
                        <select class="form-control" id="statu" name="statu" required>
                            <option value="En cours" <?php echo $row['Statu'] === 'En cours' ? 'selected' : ''; ?>>En cours</option>
                            <option value="Terminé" <?php echo $row['Statu'] === 'Terminé' ? 'selected' : ''; ?>>Terminé</option>
                        </select>
                        <div class="invalid-feedback">Veuillez sélectionner le statut du projet.</div>
                    </div>
                    <button type="submit" class="btn btn-primary">Sauvegarder</button>
                </form>
            </div>
        </div>
    </section>
</body>
</html>
<?php $conn->close(); ?>