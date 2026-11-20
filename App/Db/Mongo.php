<?php

namespace App\Db;

use MongoDB\Driver\Manager;

class Mongo
{
    private string $uri;
    private string $dbName;
    private ?Manager $manager = null;
    private static ?Mongo $_instance = null;

    private function __construct()
    {
        // Paramètres lus dans les variables d'environnement définies dans docker-compose.yml
        $this->uri = getenv('MONGO_URI') ?: 'mongodb://localhost:27017';
        $this->dbName = getenv('MONGO_DB') ?: 'mooviz';
    }

    /*
        Singleton : une seule connexion MongoDB pour toute la requête (comme Mysql.php)
    */
    public static function getInstance(): self
    {
        if (is_null(self::$_instance)) {
            self::$_instance = new Mongo();
        }
        return self::$_instance;
    }

    /*
        Le Manager du driver MongoDB : il gère la connexion au serveur
        (la connexion réelle n'est ouverte qu'à la première requête)
    */
    public function getManager(): Manager
    {
        if (is_null($this->manager)) {
            $this->manager = new Manager($this->uri);
        }
        return $this->manager;
    }

    public function getDbName(): string
    {
        return $this->dbName;
    }
}
