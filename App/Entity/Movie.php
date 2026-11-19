<?php

namespace App\Entity;

use DateTime;

class Movie extends Entity
{
    protected ?int $id = null;
    protected ?string $title = null;
    protected ?string $synopsis = null;
    protected ?DateTime $release_date = null;
    protected ?DateTime $duration = null;
    protected ?string $image_name = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): self
    {
        $this->id = $id;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSynopsis(): ?string
    {
        return $this->synopsis;
    }

    public function setSynopsis(?string $synopsis): self
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    public function getReleaseDate(): ?DateTime
    {
        return $this->release_date;
    }

    public function setReleaseDate(?DateTime $release_date): self
    {
        $this->release_date = $release_date;

        return $this;
    }

    public function getDuration(): ?DateTime
    {
        return $this->duration;
    }

    public function setDuration(?DateTime $duration): self
    {
        $this->duration = $duration;

        return $this;
    }

    public function getImageName(): ?string
    {
        return $this->image_name;
    }

    public function setImageName(?string $image_name): self
    {
        $this->image_name = $image_name;

        return $this;
    }

    public function getImagePath(): string
    {
        if ($this->getImageName()) {
            return MOVIES_IMAGES_FOLDER . $this->getImageName();
        } else {
            return ASSETS_IMAGES_FOLDER . 'default-movie.png';
        }
    }

    /*
        Vérifie les données du formulaire d'ajout ou de modification d'un film
    */
    public function validate(): array
    {
        $errors = [];
        if (empty($this->getTitle())) {
            $errors['title'] = 'Le titre ne doit pas être vide';
        } else if (mb_strlen($this->getTitle()) > 255) {
            $errors['title'] = 'Le titre ne doit pas dépasser 255 caractères';
        }
        if (empty($this->getSynopsis())) {
            $errors['synopsis'] = 'Le synopsis ne doit pas être vide';
        }
        return $errors;
    }
}
