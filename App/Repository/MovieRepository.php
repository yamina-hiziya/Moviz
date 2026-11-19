<?php

namespace App\Repository;

use App\Entity\Movie;

class MovieRepository extends Repository
{
    public function findOneById(int $id): Movie|bool
    {
        $query = $this->pdo->prepare("SELECT * FROM movie WHERE id = :id");
        $query->bindValue(':id', $id, $this->pdo::PARAM_INT);
        $query->execute();
        $movieData = $query->fetch($this->pdo::FETCH_ASSOC);
        if ($movieData) {
            return Movie::createAndHydrate($movieData);
        } else {
            return false;
        }
    }

    public function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM movie");
        $query->execute();
        $movies = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $moviesArray = [];

        if ($movies) {
            foreach ($movies as $movie) {
                $moviesArray[] = Movie::createAndHydrate($movie);
            }
        }
        return $moviesArray;
    }

    /*
        Vérifie si un film TMDB a déjà été importé
    */
    public function existsByTmdbId(int $tmdb_id): bool
    {
        $query = $this->pdo->prepare("SELECT COUNT(*) FROM movie WHERE tmdb_id = :tmdb_id");
        $query->bindValue(':tmdb_id', $tmdb_id, $this->pdo::PARAM_INT);
        $query->execute();

        return (int)$query->fetchColumn() > 0;
    }

    /*
        Enregistre un nouveau film et renvoie son id
        $tmdb_id vaut null pour un film ajouté à la main
    */
    public function insert(Movie $movie, ?int $tmdb_id = null): int
    {
        $query = $this->pdo->prepare(
            'INSERT INTO movie (tmdb_id, title, synopsis, release_date, duration, image_name) 
            VALUES (:tmdb_id, :title, :synopsis, :release_date, :duration, :image_name)'
        );

        $query->bindValue(':tmdb_id', $tmdb_id, $tmdb_id === null ? $this->pdo::PARAM_NULL : $this->pdo::PARAM_INT);
        $query->bindValue(':title', $movie->getTitle(), $this->pdo::PARAM_STR);
        $query->bindValue(':synopsis', $movie->getSynopsis(), $this->pdo::PARAM_STR);
        // Les dates sont converties au format attendu par MySQL (null si inconnues)
        $query->bindValue(':release_date', $movie->getReleaseDate()?->format('Y-m-d'), $this->pdo::PARAM_STR);
        $query->bindValue(':duration', $movie->getDuration()?->format('H:i:s'), $this->pdo::PARAM_STR);
        $query->bindValue(':image_name', $movie->getImageName(), $this->pdo::PARAM_STR);

        $query->execute();

        return (int)$this->pdo->lastInsertId();
    }
}
