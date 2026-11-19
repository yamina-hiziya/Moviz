<?php

namespace App\Repository;

use App\Entity\Director;

class DirectorRepository extends Repository
{
    public function findAllByMovieId(int $movie_id): array
    {
        $query = $this->pdo->prepare("SELECT d.* FROM director d
                                    INNER JOIN movie_director md 
                                    ON md.director_id = d.id 
                                    WHERE md.movie_id = :movie_id");

        $query->bindValue(':movie_id', $movie_id, $this->pdo::PARAM_INT);
        $query->execute();
        $directors = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $directorsArray = [];

        if ($directors) {
            foreach ($directors as $director) {
                $directorsArray[] = Director::createAndHydrate($director);
            }
        }
        return $directorsArray;
    }

    /*
        Renvoie l'id du réalisateur, en le créant s'il n'existe pas encore
    */
    public function findOrCreate(string $first_name, string $last_name): int
    {
        $query = $this->pdo->prepare("SELECT id FROM director WHERE first_name = :first_name AND last_name = :last_name");
        $query->bindValue(':first_name', $first_name, $this->pdo::PARAM_STR);
        $query->bindValue(':last_name', $last_name, $this->pdo::PARAM_STR);
        $query->execute();
        $id = $query->fetchColumn();

        if ($id !== false) {
            return (int)$id;
        }

        $query = $this->pdo->prepare("INSERT INTO director (first_name, last_name) VALUES (:first_name, :last_name)");
        $query->bindValue(':first_name', $first_name, $this->pdo::PARAM_STR);
        $query->bindValue(':last_name', $last_name, $this->pdo::PARAM_STR);
        $query->execute();

        return (int)$this->pdo->lastInsertId();
    }

    /*
        Relie un réalisateur à un film (table de liaison movie_director)
    */
    public function linkToMovie(int $movie_id, int $director_id): bool
    {
        $query = $this->pdo->prepare("INSERT INTO movie_director (movie_id, director_id) VALUES (:movie_id, :director_id)");
        $query->bindValue(':movie_id', $movie_id, $this->pdo::PARAM_INT);
        $query->bindValue(':director_id', $director_id, $this->pdo::PARAM_INT);

        return $query->execute();
    }
}
