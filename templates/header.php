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
        <header class="border-bottom mb-4">
            <nav class="navbar navbar-expand-lg py-3">

                <!-- Logo -->
                <a href="/" class="navbar-brand me-lg-5">
                    <img src="/assets/images/logo-moviz.svg" alt="Mooviz" height="80">
                </a>

                <!-- Bouton burger : visible seulement sur tablette et téléphone -->
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar" aria-controls="mainNavbar" aria-expanded="false" aria-label="Ouvrir le menu">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <!-- Contenu repliable : menu + boutons de compte -->
                <div class="collapse navbar-collapse" id="mainNavbar">

                    <ul class="navbar-nav nav-pills mx-lg-auto gap-lg-2 my-3 my-lg-0">
                        <li class="nav-item">
                            <a href="/" class="nav-link px-3 <?= NavigationTools::addActiveClass('page', 'home') ?>">Accueil</a>
                        </li>
                        <li class="nav-item">
                            <a href="/index.php?controller=movie&action=list" class="nav-link px-3 <?= NavigationTools::addActiveClass('movie', 'list') ?>">Les films</a>
                        </li>
                        <?php if (User::isAdmin()) { ?>
                            <li class="nav-item">
                                <a href="/index.php?controller=admin&action=dashboard" class="nav-link px-3 <?= NavigationTools::addActiveClass('admin', 'dashboard') ?>">Administration</a>
                            </li>
                        <?php } ?>
                    </ul>

                    <div class="d-flex flex-wrap align-items-center gap-2">
                        <?php if (User::isLogged()) { ?>
                            <span class="small me-1">Bonjour <?= htmlspecialchars($_SESSION['user']['first_name']) ?></span>
                            <a href="/index.php?controller=auth&action=logout" class="btn btn-outline-primary">Déconnexion</a>
                        <?php } else { ?>
                            <a href="/index.php?controller=auth&action=login" class="btn btn-outline-primary <?= NavigationTools::addActiveClass('auth', 'login') ?>">Connexion</a>
                            <a href="/index.php?controller=user&action=register" class="btn btn-primary <?= NavigationTools::addActiveClass('user', 'register') ?>">Inscription</a>
                        <?php } ?>
                    </div>

                </div>
            </nav>
        </header>

        <main>