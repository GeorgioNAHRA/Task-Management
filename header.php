<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gate - Gestion de projet</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="GATE.css">
</head>
<body>
    <!-- Header -->
    <header class="bg-dark text-white py-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <a href="GATE.php" class="text-white text-decoration-none"><h1 class="h3 mb-0">Gate</h1></a>
                <nav>
                    <ul class="nav">
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Produit</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Documents</a></li>
                                <li><a class="dropdown-item" href="#">Projets</a></li>
                                <li><a class="dropdown-item" href="#">Calendrier</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Ressources</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Blog</a></li>
                                <li><a class="dropdown-item" href="#">Gate Académie</a></li>
                                <li><a class="dropdown-item" href="#">Site d'aide</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Solution</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Grandes Entreprises</a></li>
                                <li><a class="dropdown-item" href="#">Petites équipes et PME</a></li>
                                <li><a class="dropdown-item" href="#">Individuel</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <button class="btn btn-dark dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">Télécharger</button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#">Gate</a></li>
                                <li><a class="dropdown-item" href="#">Gate Calendrier</a></li>
                                <li><a class="dropdown-item" href="#">Clipper</a></li>
                            </ul>
                        </li>
                        <li class="nav-item"><a href="#" class="nav-link text-white">Tarifs</a></li>
                    </ul>
                </nav>
                <div class="d-flex align-items-center">
                    <?php if ($user_info): ?>
                        <span class="me-1">
                            <?php echo htmlspecialchars($user_info['Prenom'] . ' ' . $user_info['Nom']); ?>
                            <?php if (!empty($user_info['photo'])): ?>
                                <img src="pdp/<?php echo htmlspecialchars($user_info['photo']); ?>" alt="Profile Picture" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">
                            <?php endif; ?>
                        </span>
                        <?php if (isset($_SESSION['admin_id'])): ?>
                            <a href="dashboard.php" class="btn btn-light me-2">Admin</a>
                        <?php endif; ?>
                        <?php if (isset($_SESSION['statu']) && ($_SESSION['statu'] === 'User' || $_SESSION['statu'] === 'Admin')): ?>
                            <a href="Essayer_gate.php" class="btn btn-light me-1">Essayer Gate</a>
                        <?php endif; ?>
                        <a href="compte.php" class="btn btn-outline-light ms-2">Compte</a>
                        <a href="logout.php" class="btn btn-outline-light ms-2">Se déconnecter</a>
                    <?php else: ?>
                        <button class="btn btn-outline-light me-1" data-bs-toggle="dropdown" aria-expanded="false" data-bs-auto-close="outside">Se connecter</button>
                        <div class="dropdown-menu p-4">
                            <form id="loginForm" method="post">
                                <div id="error-message" style="color: red;"></div>
                                <div class="mb-3">
                                    <label for="exampleDropdownFormEmail2" class="form-label">Adresse email</label>
                                    <input type="email" class="form-control" id="exampleDropdownFormEmail2" name="mail" placeholder="email@example.com" required>
                                </div>
                                <div class="mb-3">
                                    <label for="exampleDropdownFormPassword2" class="form-label">Mot de passe</label>
                                    <input type="password" class="form-control" id="exampleDropdownFormPassword2" name="password" placeholder="Mot de passe" required>
                                </div>
                                <button type="submit" class="btn btn-primary">Se connecter</button>
                            </form>
                        </div>
                        <a href="signup.php" class="btn btn-light">S'inscrire</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    </body>