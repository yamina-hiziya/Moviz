<?php

namespace App\Repository;

use App\Entity\Review;

class ReviewRepository extends Repository
{
    /*
        Critiques validées d'un film, avec le prénom de l'auteur (jointure avec user)
    */
    public function findApprovedByMovieId(int $movie_id): array
    {
        $query = $this->pdo->prepare("SELECT r.*, u.first_name AS user_first_name
                                    FROM review r
                                    INNER JOIN user u ON u.id = r.user_id
                                    WHERE r.movie_id = :movie_id AND r.approved = 1
                                    ORDER BY r.id DESC");

        $query->bindValue(':movie_id', $movie_id, $this->pdo::PARAM_INT);
        $query->execute();
        $reviews = $query->fetchAll($this->pdo::FETCH_ASSOC);

        $reviewsArray = [];

        if ($reviews) {
            foreach ($reviews as $review) {
                $reviewsArray[] = Review::createAndHydrate($review);
            }
        }
        return $reviewsArray;
    }

    /*
        Moyenne et nombre de notes d'un film
        Toutes les notes comptent, même si la critique n'est pas encore validée
    */
    public function getRatingStatsByMovieId(int $movie_id): array
    {
        $query = $this->pdo->prepare("SELECT AVG(rate) AS average, COUNT(id) AS total
                                    FROM review
                                    WHERE movie_id = :movie_id");

        $query->bindValue(':movie_id', $movie_id, $this->pdo::PARAM_INT);
        $query->execute();
        $stats = $query->fetch($this->pdo::FETCH_ASSOC);

        return [
            'average' => $stats['average'] !== null ? round((float)$stats['average'], 1) : null,
            'total' => (int)$stats['total'],
        ];
    }

    /*
        Avis d'un utilisateur sur un film (pour savoir s'il a déjà noté)
    */
    public function findOneByUserAndMovie(int $user_id, int $movie_id): Review|bool
    {
        $query = $this->pdo->prepare("SELECT * FROM review WHERE user_id = :user_id AND movie_id = :movie_id");
        $query->bindValue(':user_id', $user_id, $this->pdo::PARAM_INT);
        $query->bindValue(':movie_id', $movie_id, $this->pdo::PARAM_INT);
        $query->execute();
        $review = $query->fetch($this->pdo::FETCH_ASSOC);
        if ($review) {
            return Review::createAndHydrate($review);
        } else {
            return false;
        }
    }

    /*
        Enregistre un nouvel avis (toujours en attente de validation)
    */
    public function persist(Review $review): bool
    {
        $query = $this->pdo->prepare(
            'INSERT INTO review (rate, review, approved, user_id, movie_id) 
            VALUES (:rate, :review, :approved, :user_id, :movie_id)'
        );

        $query->bindValue(':rate', $review->getRate(), $this->pdo::PARAM_INT);
        $query->bindValue(':review', $review->getReview(), $this->pdo::PARAM_STR);
        $query->bindValue(':approved', $review->isApproved() ? 1 : 0, $this->pdo::PARAM_INT);
        $query->bindValue(':user_id', $review->getUserId(), $this->pdo::PARAM_INT);
        $query->bindValue(':movie_id', $review->getMovieId(), $this->pdo::PARAM_INT);

        return $query->execute();
    }
}
