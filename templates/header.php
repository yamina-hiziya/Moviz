<?php

use App\Entity\User;
use App\Tools\NavigationTools;
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mooviz</title>

    <!-- Icône de l'onglet -->
    <link rel="icon" type="image/svg+xml" href="/assets/images/favicon.svg">

    <!-- Polices : Playfair Display (titres) et Inter (texte) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5.3.3 (version stable) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Styles de Mooviz (chargés après Bootstrap pour pouvoir le surcharger) -->
    <link rel="stylesheet" href="/assets/css/override_bootstrap.css?v=<?= filemtime(_ROOTPATH_ . '/assets/css/override_bootstrap.css') ?>">
    <link rel="stylesheet" href="/assets/css/custom.css?v=<?= filemtime(_ROOTPATH_ . '/assets/css/custom.css') ?>">
</head>

<body>

    <div class="container">
        <header class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-between py-3 mb-4 border-bottom">
            <a href="/" class="d-flex align-items-center col-md-3 mb-2 mb-md-0 text-decoration-none">
                <img src="/assets/images/logo-moviz.svg" alt="Mooviz" height="80">
            </a>

            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a href="/" class="nav-link px-3 <?= NavigationTools::addActiveClass('page', 'home') ?>">Accueil</a>
                </li>
                <li class="nav-item">
                    <a href="/index.php?controller=movie&action=list" class="nav-link px-3 <?= NavigationTools::addActiveClass('movie', 'list') ?>">Les films</a>
                </li>
            </ul>

            <div class="col-md-3 text-end">
                <?php if (User::isLogged()) { ?>
                    <span class="me-2 small">Bonjour <?= htmlspecialchars($_SESSION['user']['first_name']) ?></span>
                    <a href="/index.php?controller=auth&action=logout" class="btn btn-outline-primary">Déconnexion</a>
                <?php } else { ?>
                    <a href="/index.php?controller=auth&action=login" class="btn btn-outline-primary me-2 <?= NavigationTools::addActiveClass('auth', 'login') ?>">Connexion</a>
                    <a href="/index.php?controller=user&action=register" class="btn btn-primary <?= NavigationTools::addActiveClass('user', 'register') ?>">Inscription</a>
                <?php } ?>
            </div>
        </header>

        <main>