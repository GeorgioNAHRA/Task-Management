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
   
   // Fonction pour afficher les utilisateurs
   function afficherUtilisateurs($conn) {
       $sql = "SELECT * FROM Utilisateur WHERE Statu='User'";
       $result = $conn->query($sql);
   
       if ($result->num_rows > 0) {
           while($row = $result->fetch_assoc()) {
               echo '<tr>';
               echo '<td>' . $row["IDUser"] . '</td>';
               echo '<td>' . $row["Nom"] . '</td>';
               echo '<td>' . $row["Prenom"] . '</td>';
               echo '<td>';
               echo '<form method="post" action="" style="display:inline-block;"><input type="hidden" name="user_id" value="' . $row["IDUser"] . '"><button type="submit" name="delete" class="btn btn-danger">Supprimer</button></form>';
               echo '</td>';
               echo '</tr>';
           }
       } else {
           echo "<tr><td colspan='4'>Aucun utilisateur à afficher.</td></tr>";
       }
   }
   
   // Fonction pour afficher les projets en cours
   function afficherProjetsEnCours($conn) {
       $sql = "SELECT * FROM Projet";
       $result = $conn->query($sql);
   
       if ($result->num_rows > 0) {
           while($row = $result->fetch_assoc()) {
               echo '<tr>';
               echo '<td>' . $row["IDProjet"] . '</td>';
               echo '<td>' . $row["nomProjet"] . '</td>';
               echo '<td>' . $row["descriptionProjet"] . '</td>';
               echo '<td>' . $row["Duree_projet"] . '</td>';
               echo '<td>' . $row["Statu"] . '</td>';
               echo '<td>' . $row["budget"] . '</td>';
               echo '<td>';
               echo '<form method="post" action="project.php" style="display:inline-block;"><input type="hidden" name="projet_id" value="' . $row["IDProjet"] . '"><button type="submit" class="btn btn-info">Gérer</button></form>';
               echo '</td>';
               echo '<td>';
               echo '<form method="get" action="edit_project.php" style="display:inline-block; margin-right: 10px;">';
               echo '<input type="hidden" name="id" value="' . $row["IDProjet"] . '">';
               echo '<button type="submit" class="btn btn-warning">Modifier</button>';
               echo '</form>';
               echo '<form method="post" action="" style="display:inline-block;">';
               echo '<input type="hidden" name="projet_id" value="' . $row["IDProjet"] . '">';
               echo '<button type="submit" name="supprimer_projet" class="btn btn-danger">Supprimer</button>';
               echo '</form>';
               echo '</td>';
               echo '</tr>';
           }
       } else {
           echo "<tr><td colspan='8'>Aucun projet en cours à afficher.</td></tr>";
       }
   }
   
   // User un compte utilisateur
   if (isset($_POST['accept'])) {
       $user_id = $_POST['user_id'];
       $sql = "UPDATE Utilisateur SET Statu='User' WHERE IDUser='$user_id'";
       
       if ($conn->query($sql) === TRUE) {
           echo "<p>Compte utilisateur accepté avec succès.</p>";
       } else {
           echo "<p>Erreur lors de l'acceptation du compte: " . $conn->error . "</p>";
       }
   }
   
   // Supprimer un compte utilisateur
   if (isset($_POST['delete'])) {
       $user_id = $_POST['user_id'];
       
       // Supprimer d'abord les enregistrements dépendants dans la table 'creer'
       $sql = "DELETE FROM creer WHERE IDUser='$user_id'";
       $result = $conn->query($sql);
   
       if ($result === TRUE) {
           // Ensuite, supprimer l'utilisateur de la table 'Utilisateur'
           $sql = "DELETE FROM Utilisateur WHERE IDUser='$user_id'";
           if ($conn->query($sql) === TRUE) {
               echo "<p>Compte utilisateur et les données associées supprimés avec succès.</p>";
           } else {
               echo "<p>Erreur lors de la suppression du compte utilisateur: " . $conn->error . "</p>";
           }
       } else {
           echo "<p>Erreur lors de la suppression des données associées: " . $conn->error . "</p>";
       }
   }
   
   // Modifier un projet
   if (isset($_POST['modifier_projet'])) {
       $projet_id = $_POST['projet_id'];
       $sql = "SELECT * FROM Projet WHERE IDProjet='$projet_id'";
       $result = $conn->query($sql);
   
       if ($result->num_rows > 0) {
           $row = $result->fetch_assoc();
           echo '<h2>Modifier le Projet</h2>';
           echo '<form method="post" action="">';
           echo '<input type="hidden" name="projet_id" value="' . $row["IDProjet"] . '">';
           echo '<label for="nom_projet">Nom du Projet:</label>';
           echo '<input type="text" id="nom_projet" name="nom_projet" value="' . $row["nomProjet"] . '" required>';
           echo '<br>';
           echo '<label for="description">Description:</label>';
           echo '<textarea id="description" name="description" required>' . $row["descriptionProjet"] . '</textarea>';
           echo '<br>';
           echo '<label for="duree_projet">Durée du Projet:</label>';
           echo '<input type="text" id="duree_projet" name="duree_projet" value="' . $row["Duree_projet"] . '" required>';
           echo '<br>';
           echo '<label for="statu">Statut:</label>';
           echo '<input type="text" id="statu" name="statu" value="' . $row["Statu"] . '" required>';
           echo '<br>';
           echo '<label for="budget">Budget:</label>';
           echo '<input type="number" id="budget" name="budget" value="' . $row["budget"] . '" required>';
           echo '<br>';
           echo '<button type="submit" name="sauvegarder_projet">Sauvegarder</button>';
           echo '</form>';
       }
   }
   
   // Sauvegarder les modifications d'un projet
   if (isset($_POST['sauvegarder_projet'])) {
       $projet_id = $_POST['projet_id'];
       $nom_projet = $_POST['nom_projet'];
       $description = $_POST['description'];
       $duree_projet = $_POST['duree_projet'];
       $statu = $_POST['statu'];
       $budget = $_POST['budget'];
   
       $sql = "UPDATE Projet SET nomProjet='$nom_projet', Duree_projet='$duree_projet', descriptionProjet='$description', Statu='$statu', budget='$budget' WHERE IDProjet='$projet_id'";
       
       if ($conn->query($sql) === TRUE) {
           echo "<p>Projet modifié avec succès.</p>";
       } else {
           echo "<p>Erreur lors de la modification du projet: " . $conn->error . "</p>";
       }
   }
   
   // Supprimer un projet
   if (isset($_POST['supprimer_projet'])) {
       $projet_id = $_POST['projet_id'];
   
       // Supprimer d'abord les enregistrements dépendants dans la table 'commentaire'
       $sql = "SELECT IDTache FROM Tache WHERE IDProjet_avoir='$projet_id'";
       $result = $conn->query($sql);
       if ($result->num_rows > 0) {
           while ($row = $result->fetch_assoc()) {
               $idtache = $row['IDTache'];
               $sql = "DELETE FROM commentaire WHERE IDTache_contenir2='$idtache'";
               if (!$conn->query($sql)) {
                   echo "<p>Erreur lors de la suppression des commentaires de la tâche $idtache: " . $conn->error . "</p>";
                   exit;
               }
           }
       }
   
       // Tables à supprimer avec la contrainte sur la colonne appropriée
       $tables = [
           'notifier' => 'IDProjet',
           'creer' => 'IDProjet',
           'dossier' => 'IDProjet__contenir1',
           'modifier' => 'IDProjet'
       ];
   
       foreach ($tables as $table => $column) {
           $sql = "DELETE FROM $table WHERE $column='$projet_id'";
           if (!$conn->query($sql)) {
               echo "<p>Erreur lors de la suppression des données associées dans la table '$table': " . $conn->error . "</p>";
               exit;
           }
       }
   
       // Enfin, supprimer le projet de la table 'Projet'
       $sql = "DELETE FROM Projet WHERE IDProjet='$projet_id'";
       if ($conn->query($sql) === TRUE) {
           echo "<p>Projet supprimé avec succès.</p>";
       } else {
           echo "<p>Erreur lors de la suppression du projet: " . $conn->error . "</p>";
       }
   }
   ?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
   <head>
      <meta charset="UTF-8" />
      <title>Admin Dashboard</title>
      <link rel="stylesheet" href="css/dashboard.css" />
      <!-- Boxicons CDN Link -->
      <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
      <link rel="stylesheet" href="css/user_management.css">
      <script type="text/javascript" src="js/sidebar.js"></script>
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
   </head>
   <body>
      <?php include('sidebar.php'); ?>
      <section class="home-section">
         <?php include('header_management.php'); ?>
         <div class="home-content">
            <div class="users-table">
               <h2>Utilisateurs enregistrés</h2>
               <table>
                  <thead>
                     <tr>
                        <th>ID</th>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Action</th>
                     </tr>
                  </thead>
                  <tbody id="registered-users">
                     <?php afficherUtilisateurs($conn); ?>
                  </tbody>
               </table>
            </div>
         </div>
      </section>
   </body>
</html>

<?php
   $conn->close();
?>