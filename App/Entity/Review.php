<?php

namespace App\Entity;

class Review extends Entity
{
    protected ?int $id = null;
    protected ?int $rate = null;
    protected ?string $review = null;
    protected bool $approved = false;
    protected ?int $user_id = null;
    protected ?int $movie_id = null;
    // Pas une colonne de la table review : récupéré par jointure avec la table user
    protected ?string $user_first_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getRate(): ?int
    {
        return $this->rate;
    }

    public function setRate(?int $rate): self
    {
        $this->rate = $rate;

        return $this;
    }

    public function getReview(): ?string
    {
        return $this->review;
    }

    public function setReview(?string $review): self
    {
        $this->review = $review;

        return $this;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;

        return $this;
    }

    public function getUserId(): ?int
    {
        return $this->user_id;
    }

    public function setUserId(?int $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getMovieId(): ?int
    {
        return $this->movie_id;
    }

    public function setMovieId(?int $movie_id): self
    {
        $this->movie_id = $movie_id;

        return $this;
    }

    public function getUserFirstName(): ?string
    {
        return $this->user_first_name;
    }

    public function setUserFirstName(?string $user_first_name): self
    {
        $this->user_first_name = $user_first_name;

        return $this;
    }

    /*
        Vérifie les données avant l'enregistrement
    */
    public function validate(): array
    {
        $errors = [];
        if ($this->getRate() === null || $this->getRate() < 1 || $this->getRate() > 5) {
            $errors['rate'] = 'Merci de choisir une note entre 1 et 5 étoiles';
        }
        if ($this->getReview() !== null && mb_strlen($this->getReview()) > 2000) {
            $errors['review'] = 'La critique ne doit pas dépasser 2000 caractères';
        }
        return $errors;
    }
}
