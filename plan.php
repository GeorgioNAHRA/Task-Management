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
   
   // Connexion à la base de données
   $servername = "localhost";
   $username = "root";
   $password = "";
   $dbname = "MNB_data";
   
   $conn = new mysqli($servername, $username, $password, $dbname);
   if ($conn->connect_error) {
       die("Connection failed: " . $conn->connect_error);
   }
   
   // Initialisation
   $projet = null;
   $taches = [];
   $message = '';
   $upload_dir = __DIR__ . '/files';
   
   // Crée le dossier /files s'il n'existe pas
   if (!is_dir($upload_dir)) {
       mkdir($upload_dir, 0777, true);
   }
   
   // Récupération des informations du projet
   if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_projet'])) {
       $id_projet = $_POST['id_projet'];
       $_SESSION['id_projet'] = $id_projet;
   } elseif (isset($_SESSION['id_projet'])) {
       $id_projet = $_SESSION['id_projet'];
   } else {
       echo "<p>Erreur : Aucun projet sélectionné.</p>";
       exit();
   }
   
   // Récupérer les détails du projet
   $projet = $conn->query("SELECT * FROM Projet WHERE IDProjet = '$id_projet'")->fetch_assoc();
   if (!$projet) {
       echo "<p>Erreur : Projet introuvable.</p>";
       exit();
   }
   
   // Vérifier si l'utilisateur a accès au projet
   $project_users = explode(',', $projet['IDUsers']);
   if ($_SESSION['statu'] !== 'Admin' && !in_array($_SESSION['user_id'], $project_users)) {
       echo "<p>Erreur : Vous n'avez pas accès à ce projet.</p>";
       exit();
   }
   
   // Utilisateurs associés au projet
   $users = $conn->query("SELECT * FROM Utilisateur");
   $taches = $conn->query("SELECT * FROM Tache WHERE IDProjet = '$id_projet'");
   
   // Mettre à jour les utilisateurs associés
   if (isset($_POST['update_users'])) {
       $selected_users = isset($_POST['project_users']) ? $_POST['project_users'] : [];
       if ($_SESSION['statu'] === 'User' && !in_array($_SESSION['user_id'], $selected_users)) {
           // L'utilisateur connecté doit toujours être inclus
           $selected_users[] = $_SESSION['user_id'];
       }
       $updated_users = implode(',', $selected_users);
   
       $conn->query("UPDATE Projet SET IDUsers = '$updated_users' WHERE IDProjet = '$id_projet'");
       $message = "Utilisateurs associés au projet mis à jour.";
       echo "<script>location.reload();</script>";
   }
   
   // Ajouter une nouvelle tâche
   if (isset($_POST['add_task'])) {
       $task_name = $conn->real_escape_string($_POST['task_name']);
       $task_description = $conn->real_escape_string($_POST['task_description']);
       $task_start = $conn->real_escape_string($_POST['task_start']);
       $task_deadline = $conn->real_escape_string($_POST['task_deadline']);
       $task_users = isset($_POST['task_users']) ? implode(',', $_POST['task_users']) : '';
   
       // Insérer une nouvelle tâche
       $insert_query = "INSERT INTO Tache (Titre, description, datedebut, datefin, IDUser, IDProjet) 
                        VALUES ('$task_name', '$task_description', '$task_start', '$task_deadline', '$task_users', '$id_projet')";
       
       if (!$conn->query($insert_query)) {
           $error_message = "Erreur lors de l'ajout de la tâche : " . $conn->error;
           echo "<script>alert('$error_message');</script>";
       } else {
           // Aucun message ou fenêtre si la tâche est créée avec succès
           echo "<script>location.reload();</script>";
       }
   }
   
   // Modifier une tâche existante
   if (isset($_POST['edit_task'])) {
       $task_id = $_POST['task_id'];
       $task_name = $_POST['task_name'];
       $task_description = $_POST['task_description'];
       $task_start = $_POST['task_start'];
       $task_deadline = $_POST['task_deadline'];
       $task_users = isset($_POST['task_users']) ? implode(',', $_POST['task_users']) : '';
   
       $conn->query("UPDATE Tache SET Titre = '$task_name', description = '$task_description', datedebut = '$task_start', datefin = '$task_deadline', IDUser = '$task_users' WHERE IDTache = '$task_id'");
       $message = "Tâche modifiée avec succès.";
       echo "<script>location.reload();</script>";
   }
   
   // Supprimer une tâche existante
   if (isset($_POST['delete_task'])) {
       $task_id = $_POST['delete_task_id'];
   
       // Supprimer la tâche de la base de données
       $delete_query = "DELETE FROM Tache WHERE IDTache = '$task_id'";
       if ($conn->query($delete_query)) {
           echo "<script>location.reload();</script>";
       } else {
           echo "<script>alert('Erreur lors de la suppression de la tâche : " . $conn->error . "');</script>";
       }
   }
   
   // Ajouter un fichier
   if (isset($_FILES['file'])) {
       $file_name = $_FILES['file']['name'];
       $file_tmp = $_FILES['file']['tmp_name'];
       $file_path = $upload_dir . '/' . basename($file_name);
   
       if (move_uploaded_file($file_tmp, $file_path)) {
           $conn->query("INSERT INTO Files (FileName, FilePath, UploadedBy, IDProjet) VALUES ('$file_name', '$file_path', '{$_SESSION['user_id']}', '$id_projet')");
           $message = "Fichier uploadé avec succès.";
           echo "<script>location.reload();</script>";
       } else {
           $message = "Erreur lors de l'upload du fichier.";
       }
   }
   
   // Suppression d'un fichier
   if (isset($_POST['delete_file'])) {
       $file_id = $_POST['file_id'];
       $file_data = $conn->query("SELECT * FROM Files WHERE IDFile = '$file_id'")->fetch_assoc();
       if ($file_data) {
           unlink($file_data['FilePath']);
           $conn->query("DELETE FROM Files WHERE IDFile = '$file_id'");
           $message = "Fichier supprimé avec succès.";
           echo "<script>location.reload();</script>";
       }
   }
   
   // Récupération des fichiers liés au projet
   $files = $conn->query("SELECT * FROM Files WHERE IDProjet = '$id_projet'");
   
   // Gestion de la chatbox : Ajouter un message
   if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty($_POST['chat_message'])) {
       $chat_message = $conn->real_escape_string($_POST['chat_message']);
       $user_id = $_SESSION['user_id'];
   
       $conn->query("INSERT INTO ChatMessages (IDUser, IDProjet, Message, Date) VALUES ('$user_id', '$id_projet', '$chat_message', NOW())");
       echo "<script>location.reload();</script>";
   }
   
   // Récupérer la date actuelle
   $current_date = date('Y-m-d');
   
   // Récupérer les tâches en retard ou à échéance
   $alert_taches = $conn->query("
       SELECT t.IDTache, t.Titre, t.description, t.datefin, p.nomProjet, u.Prenom, u.Nom
       FROM Tache t
       JOIN Projet p ON t.IDProjet = p.IDProjet
       LEFT JOIN Utilisateur u ON FIND_IN_SET(u.IDUser, t.IDUser)
       WHERE t.datefin <= '$current_date'
       ORDER BY t.datefin ASC
   ");
   
   ?>
<!DOCTYPE html>
<html lang="fr" dir="ltr">
   <head>
      <meta charset="UTF-8" />
      <title>Nom du Projet</title>
      <link rel="stylesheet" href="dashboard.css" />
      <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
      <link href="https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css" rel="stylesheet" />
      <meta name="viewport" content="width=device-width, initial-scale=1.0" />
      <style>
         .file-upload {
         border: 2px dashed #ccc;
         border-radius: 10px;
         padding: 20px;
         text-align: center;
         margin-top: 20px;
         }
         .file-upload.dragover {
         border-color: #0e98e6;
         background-color: #f1f9ff;
         }
      </style>
   </head>
   <body>
      <?php include('sidebar.php'); ?>
      <section class="home-section">
         <?php include('header_gestion.php'); ?>
         <div class="home-content">
            <div class="project-details">
               <h2><strong>Nom du Projet :</strong> <?= htmlspecialchars($projet['nomProjet']) ?></h2>
               <!-- Afficher les messages -->
               <?php if ($message): ?>
               <div class="alert alert-success">
                  <?= htmlspecialchars($message) ?>
               </div>
               <?php endif; ?>
               <!-- Section des alertes -->
               <h4 class="mt-5 text-warning">Alertes</h4>
               <div class="alert alert-warning">
                  <h5>Tâches en retard ou à échéance</h5>
                  <?php if ($alert_taches->num_rows > 0): ?>
                  <table class="table table-striped">
                     <thead>
                        <tr>
                           <th>ID</th>
                           <th>Titre</th>
                           <th>Description</th>
                           <th>Projet</th>
                           <th>Assigné à</th>
                           <th>Date d'échéance</th>
                        </tr>
                     </thead>
                     <tbody>
                        <?php while ($tache = $alert_taches->fetch_assoc()): ?>
                        <tr>
                           <td><?= htmlspecialchars($tache['IDTache']) ?></td>
                           <td><?= htmlspecialchars($tache['Titre']) ?></td>
                           <td><?= htmlspecialchars($tache['description']) ?></td>
                           <td><?= htmlspecialchars($tache['nomProjet']) ?></td>
                           <td><?= htmlspecialchars($tache['Prenom'] . ' ' . $tache['Nom']) ?></td>
                           <td><?= htmlspecialchars(date('d/m/Y', strtotime($tache['datefin']))) ?></td>
                        </tr>
                        <?php endwhile; ?>
                     </tbody>
                  </table>
                  <?php else: ?>
                  <p>Aucune tâche en retard ou arrivant à échéance.</p>
                  <?php endif; ?>
               </div>
               <!-- Gestion des utilisateurs associés -->
               <h3>Gestion des utilisateurs associés</h3>
               <form method="post" action="">
                  <div class="form-group">
                     <label for="project_users">Utilisateurs disponibles :</label>
                     <div>
                        <?php while ($user = $users->fetch_assoc()): ?>
                        <?php
                           $is_current_user = $user['IDUser'] == $_SESSION['user_id'];
                           $disabled = ($_SESSION['statu'] === 'User' && $is_current_user) ? 'disabled' : '';
                           ?>
                        <div class="form-check">
                           <input type="checkbox" class="form-check-input" id="user_<?= $user['IDUser'] ?>" name="project_users[]" value="<?= $user['IDUser'] ?>"
                              <?= in_array($user['IDUser'], $project_users) ? 'checked' : '' ?> <?= $disabled ?>>
                           <label class="form-check-label" for="user_<?= $user['IDUser'] ?>">
                           <?= htmlspecialchars($user['Prenom'] . " " . $user['Nom']) ?>
                           <?= $is_current_user ? '(Vous)' : '' ?>
                           </label>
                        </div>
                        <?php endwhile; ?>
                     </div>
                  </div>
                  <button type="submit" name="update_users" class="btn btn-primary">Mettre à jour</button>
               </form>
               <br>
               <!-- Gestion des tâches -->
               <h3>Gestion des Tâches</h3>
               <form method="post" action="">
                  <h4>Ajouter une nouvelle tâche</h4>
                  <div class="form-group">
                     <label for="task_name">Nom de la tâche :</label>
                     <input type="text" id="task_name" name="task_name" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label for="task_description">Description :</label>
                     <textarea id="task_description" name="task_description" class="form-control" required></textarea>
                  </div>
                  <div class="form-group">
                     <label for="task_start">Date de début :</label>
                     <input type="date" id="task_start" name="task_start" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label for="task_deadline">Date limite :</label>
                     <input type="date" id="task_deadline" name="task_deadline" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label for="task_users">Utilisateurs associés :</label>
                     <div>
                        <?php foreach ($project_users as $user_id): ?>
                        <?php $user_data = $conn->query("SELECT * FROM Utilisateur WHERE IDUser = '$user_id'")->fetch_assoc(); ?>
                        <?php if ($user_data): ?>
                        <div class="form-check">
                           <input type="checkbox" class="form-check-input" id="task_user_<?= $user_data['IDUser'] ?>" name="task_users[]" value="<?= $user_data['IDUser'] ?>">
                           <label class="form-check-label" for="task_user_<?= $user_data['IDUser'] ?>">
                           <?= htmlspecialchars($user_data['Prenom'] . " " . $user_data['Nom']) ?>
                           </label>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                     </div>
                  </div>
                  <button type="submit" name="add_task" class="btn btn-success">Ajouter</button>
               </form>
               <br>
               <!-- Tâches existantes -->
               <h4>Tâches existantes</h4>
               <?php if ($taches->num_rows > 0): ?>
               <?php while ($tache = $taches->fetch_assoc()): ?>
               <form method="post" action="" class="d-inline-block">
                  <input type="hidden" name="task_id" value="<?= $tache['IDTache'] ?>">
                  <div class="form-group">
                     <label>Nom :</label>
                     <input type="text" name="task_name" value="<?= htmlspecialchars($tache['Titre']) ?>" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Description :</label>
                     <textarea name="task_description" class="form-control" required><?= htmlspecialchars($tache['description']) ?></textarea>
                  </div>
                  <div class="form-group">
                     <label>Date de début :</label>
                     <input type="date" name="task_start" value="<?= htmlspecialchars($tache['datedebut']) ?>" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Date limite :</label>
                     <input type="date" name="task_deadline" value="<?= htmlspecialchars($tache['datefin']) ?>" class="form-control" required>
                  </div>
                  <div class="form-group">
                     <label>Utilisateurs associés :</label>
                     <div>
                        <?php foreach ($project_users as $user_id): ?>
                        <?php $user_data = $conn->query("SELECT * FROM Utilisateur WHERE IDUser = '$user_id'")->fetch_assoc(); ?>
                        <?php if ($user_data): ?>
                        <div class="form-check">
                           <input type="checkbox" class="form-check-input" id="edit_task_user_<?= $user_data['IDUser'] ?>" name="task_users[]" value="<?= $user_data['IDUser'] ?>" <?= in_array($user_data['IDUser'], explode(',', $tache['IDUser'])) ? 'checked' : '' ?>>
                           <label class="form-check-label" for="edit_task_user_<?= $user_data['IDUser'] ?>">
                           <?= htmlspecialchars($user_data['Prenom'] . " " . $user_data['Nom']) ?>
                           </label>
                        </div>
                        <?php endif; ?>
                        <?php endforeach; ?>
                     </div>
                  </div>
                  <!-- Boutons Modifier et Supprimer -->
                  <div class="btn-group" role="group">
                     <button type="submit" name="edit_task" class="btn btn-warning" style="margin-right: 5px;">Modifier</button>
               <form method="post" action="" class="d-inline">
               <input type="hidden" name="delete_task_id" value="<?= $tache['IDTache'] ?>">
               <button type="submit" name="delete_task" class="btn btn-danger">Supprimer</button>
               </form>
               </div>
               </form>
               <hr>
               <?php endwhile; ?>
               <?php else: ?>
               <p>Aucune tâche associée.</p>
               <?php endif; ?>
               <!-- Section des fichiers -->
               <h4>Fichiers associés</h4>
               <form id="file-upload-form" enctype="multipart/form-data" method="post">
                  <div id="file-upload-area" class="file-upload">
                     Glissez et déposez vos fichiers ici, ou cliquez pour sélectionner.
                     <input type="file" id="file-input" name="file" style="display: none;">
                  </div>
               </form>
               <h4 class="mt-4">Fichiers existants</h4>
               <?php if ($files->num_rows > 0): ?>
               <ul>
                  <?php while ($file = $files->fetch_assoc()): ?>
                  <li>
                     <a href="<?= htmlspecialchars($file['FilePath']) ?>" download>
                     <?= htmlspecialchars($file['FileName']) ?>
                     </a>
                     <form method="post" action="" style="display: inline;">
                        <input type="hidden" name="file_id" value="<?= $file['IDFile'] ?>">
                        <button type="submit" name="delete_file" class="btn btn-danger btn-sm">Supprimer</button>
                     </form>
                  </li>
                  <?php endwhile; ?>
               </ul>
               <?php else: ?>
               <p>Aucun fichier associé.</p>
               <?php endif; ?>
               <!-- Section de la chatbox -->
               <h4>Chatbox</h4>
               <div class="chatbox-container">
                  <div class="chat-messages">
                     <?php
                        $chatMessages = $conn->query("SELECT cm.*, u.Prenom, u.Nom 
                                                      FROM ChatMessages cm 
                                                      JOIN Utilisateur u ON u.IDUser = cm.IDUser 
                                                      WHERE cm.IDProjet = '$id_projet' 
                                                      ORDER BY cm.Date ASC");
                        
                        if ($chatMessages->num_rows > 0):
                            while ($message = $chatMessages->fetch_assoc()):
                        ?>
                     <div class="message">
                        <span class="date"><?= htmlspecialchars($message['Date']) ?></span>
                        <strong class="username"><?= htmlspecialchars($message['Prenom'] . ' ' . $message['Nom']) ?>:</strong>
                        <span class="text"><?= htmlspecialchars($message['Message']) ?></span>
                     </div>
                     <?php
                        endwhile;
                        else:
                        ?>
                     <p>Aucun message pour le moment.</p>
                     <?php endif; ?>
                  </div>
                  <form method="post" action="">
                     <div class="form-group">
                        <textarea name="chat_message" class="form-control" placeholder="Entrez votre message..." required></textarea>
                     </div>
                     <button type="submit" class="btn btn-primary">Envoyer</button>
                  </form>
               </div>
            </div>
         </div>
      </section>
      <script>
         // Drag-and-drop file upload
         const uploadArea = document.getElementById('file-upload-area');
         const fileInput = document.getElementById('file-input');
         
         uploadArea.addEventListener('click', () => fileInput.click());
         
         fileInput.addEventListener('change', () => {
             const form = document.getElementById('file-upload-form');
             form.submit();
         });
         
         uploadArea.addEventListener('dragover', (e) => {
             e.preventDefault();
             uploadArea.classList.add('dragover');
         });
         
         uploadArea.addEventListener('dragleave', () => {
             uploadArea.classList.remove('dragover');
         });
         
         uploadArea.addEventListener('drop', (e) => {
             e.preventDefault();
             fileInput.files = e.dataTransfer.files;
             const form = document.getElementById('file-upload-form');
             form.submit();
         });
      </script>
      </section>
      <style>
         .home-content {
         padding: 20px;
         }
         .project-details {
         background: #fff;
         padding: 20px;
         border-radius: 10px;
         box-shadow: 0 5px 10px rgba(0, 0, 0, 0.1);
         margin-top: 20px;
         }
         .project-details h2, .project-details h3 {
         margin-bottom: 15px;
         }
         .project-details ul {
         list-style-type: disc;
         margin-left: 20px;
         }
         .project-details h2 {
         font-weight: normal;
         }
         .project-details h2 strong {
         font-weight: bold;
         }
      </style>
   </body>
</html>
<?php $conn->close(); ?>