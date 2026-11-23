<?php

/*
    Configuration de la connexion MySQL
*/

return [
    'db_name' => getenv('DB_NAME') ?: 'moviz',
    'db_user' => getenv('DB_USER') ?: 'moviz',
    'db_password' => getenv('DB_PASSWORD') ?: '',
    'db_port' => getenv('DB_PORT') ?: '3306',
    'db_host' => getenv('DB_HOST') ?: 'db',
];
