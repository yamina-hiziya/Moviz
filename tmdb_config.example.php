<?php

/*
    Configuration de l'API TMDB (facultative : sert uniquement au script bin/import_tmdb.php)

    1. Copier ce fichier :  cp tmdb_config.example.php tmdb_config.php
    2. Coller son « Jeton d'accès en lecture à l'API » (themoviedb.org > Paramètres > API)
    Le fichier tmdb_config.php est exclu de Git (.gitignore) : la clé n'est jamais publiée.
*/

return [
    // Jeton d'accès en lecture à l'API (themoviedb.org > Paramètres > API)
    'token' => 'COLLE_TON_JETON_ICI',
    // Adresse de base de l'API
    'api_url' => 'https://api.themoviedb.org/3',
    // Adresse de base des affiches (w500 = largeur 500 px)
    'image_url' => 'https://image.tmdb.org/t/p/w500',
    // Langue des titres et synopsis
    'language' => 'fr-FR',
    // Nombre de pages de films populaires à importer (20 films par page)
    'pages' => 2,
];
