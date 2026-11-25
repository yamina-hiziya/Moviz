<?php
define("ROLE_USER", "user");
define("ROLE_ADMIN", "admin");

// Chemins des images, depuis la racine du projet (servent aussi pour enregistrer les fichiers sur le disque)
define("MOVIES_IMAGES_FOLDER", "/uploads/movies/");
define("ASSETS_IMAGES_FOLDER", "/assets/images/");

// Préfixe des adresses du site : vide en local, "/mooviz" en ligne (variable d'environnement BASE_URL)
// rtrim retire un éventuel "/" final pour éviter les doubles barres
define("BASE_URL", rtrim(getenv('BASE_URL') ?: '', '/'));
