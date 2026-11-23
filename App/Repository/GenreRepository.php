<?php

namespace App\Repository;

use App\Entity\Genre;

class GenreRepository extends Repository
{
    /*
        Tous les genres, triés par nom (pour les cases à cocher du formulaire admin)
    */
    public function findAll(): array
    {
        $query = $this->pdo->prepare("SELECT * FROM genre ORDER BY name ASC");
        $query->execute();
        $genres = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $genresArray = [];

        if ($genres) {
            foreach ($genres as $genre) {
                $genresArray[] = Genre::createAndHydrate($genre);
            }
        }
        return $genresArray;
    }

    public function findAllByMovieId(int $movie_id): array
    {
        $query = $this->pdo->prepare("SELECT g.* FROM genre g
                                    INNER JOIN movie_genre mg 
                                    ON mg.genre_id = g.id 
                                    WHERE mg.movie_id = :movie_id");

        $query->bindValue(':movie_id', $movie_id, $this->pdo::PARAM_INT);
        $query->execute();
        $genres = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $genresArray = [];

        if ($genres) {
            foreach ($genres as $genre) {
                $genresArray[] = Genre::createAndHydrate($genre);
            }
        }
        return $genresArray;
    }

    /*
        Renvoie l'id du genre portant ce nom, en le créant s'il n'existe pas encore
    */
    public function findOrCreateByName(string $name): int
    {
        $query = $this->pdo->prepare("SELECT id FROM genre WHERE name = :name");
        $query->bindValue(':name', $name, $this->pdo::PARAM_STR);
        $query->execute();
        $id = $query->fetchColumn();

        if ($id !== false) {
            return (int)$id;
        }

        $query = $this->pdo->prepare("INSERT INTO genre (name) VALUES (:name)");
        $query->bindValue(':name', $name, $this->pdo::PARAM_STR);
        $query->execute();

        return (int)$this->pdo->lastInsertId();
    }

    /*
        Relie un genre à un film (table de liaison movie_genre)
    */
    public function linkToMovie(int $movie_id, int $genre_id): bool
    {
        $query = $this->pdo->prepare("INSERT INTO movie_genre (movie_id, genre_id) VALUES (:movie_id, :genre_id)");
        $query->bindValue(':movie_id', $movie_id, $this->pdo::PARAM_INT);
        $query->bindValue(':genre_id', $genre_id, $this->pdo::PARAM_INT);

        return $query->execute();
    }

    /*
        Retire tous les genres d'un film (avant de remettre ceux cochés dans le formulaire)
    */
    public function unlinkAllFromMovie(int $movie_id): bool
    {
        $query = $this->pdo->prepare("DELETE FROM movie_genre WHERE movie_id = :movie_id");
        $query->bindValue(':movie_id', $movie_id, $this->pdo::PARAM_INT);

        return $query->execute();
    }
}
