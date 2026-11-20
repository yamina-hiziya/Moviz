<?php

namespace App\Repository;

use App\Db\Mongo;
use MongoDB\BSON\UTCDateTime;
use MongoDB\Driver\BulkWrite;
use MongoDB\Driver\Manager;
use MongoDB\Driver\Query;

/*
    Statistiques de consultation des fiches films, stockées dans MongoDB
    Un document par film : { movie_id: 12, views: 37, last_viewed_at: ISODate(...) }
*/

class MovieViewRepository
{
    private const COLLECTION = 'movie_views';

    private Manager $manager;
    // Espace de noms MongoDB : "base.collection", par exemple "mooviz.movie_views"
    private string $namespace;

    public function __construct()
    {
        $mongo = Mongo::getInstance();
        $this->manager = $mongo->getManager();
        $this->namespace = $mongo->getDbName() . '.' . self::COLLECTION;
    }

    /*
        Ajoute une vue à un film
        upsert : si le film n'a pas encore de document, MongoDB le crée
    */
    public function addView(int $movie_id): void
    {
        $bulk = new BulkWrite();
        $bulk->update(
            ['movie_id' => $movie_id],
            [
                '$inc' => ['views' => 1],
                '$set' => ['last_viewed_at' => new UTCDateTime()],
            ],
            ['upsert' => true]
        );

        $this->manager->executeBulkWrite($this->namespace, $bulk);
    }

    /*
        Nombre de vues d'un film (0 s'il n'a jamais été consulté)
    */
    public function getViews(int $movie_id): int
    {
        $query = new Query(['movie_id' => $movie_id], ['limit' => 1]);
        $cursor = $this->manager->executeQuery($this->namespace, $query);

        foreach ($cursor as $document) {
            return (int)$document->views;
        }
        return 0;
    }

    /*
        Nombre de vues de plusieurs films en une seule requête
        Renvoie [movie_id => vues]
    */
    public function getViewsForMovies(array $movie_ids): array
    {
        $query = new Query(['movie_id' => ['$in' => array_values($movie_ids)]]);
        $cursor = $this->manager->executeQuery($this->namespace, $query);

        $views = [];
        foreach ($cursor as $document) {
            $views[(int)$document->movie_id] = (int)$document->views;
        }
        return $views;
    }

    /*
        Les films les plus consultés, du plus vu au moins vu
        Renvoie [movie_id => vues]
    */
    public function findMostViewed(int $limit): array
    {
        $query = new Query([], [
            'sort' => ['views' => -1],
            'limit' => $limit,
        ]);
        $cursor = $this->manager->executeQuery($this->namespace, $query);

        $views = [];
        foreach ($cursor as $document) {
            $views[(int)$document->movie_id] = (int)$document->views;
        }
        return $views;
    }

    /*
        Supprime les statistiques d'un film (quand le film est supprimé)
    */
    public function deleteByMovieId(int $movie_id): void
    {
        $bulk = new BulkWrite();
        $bulk->delete(['movie_id' => $movie_id]);

        $this->manager->executeBulkWrite($this->namespace, $bulk);
    }
}
