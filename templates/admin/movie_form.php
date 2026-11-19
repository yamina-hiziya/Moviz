<?php require_once _TEMPLATEPATH_ . '/header.php';
/**
 * @var App\Entity\Movie $movie
 * @var App\Entity\Genre[] $genres
 * @var App\Entity\Director[] $directors
 * @var int[] $selectedGenreIds
 * @var int[] $selectedDirectorIds
 * @var array $errors
 * @var string $pageTitle
 */
?>

<h1 class="mb-4"><?= htmlspecialchars($pageTitle) ?></h1>

<?php if (!empty($errors)) { ?>
    <div class="alert alert-danger" role="alert">
        Le formulaire contient des erreurs, merci de les corriger.
    </div>
<?php } ?>

<!-- enctype="multipart/form-data" est obligatoire pour envoyer un fichier -->
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="csrf_token" value="<?= \App\Tools\Security::getCsrfToken() ?>">

    <div class="row g-4">

        <!-- Colonne de gauche : informations du film -->
        <div class="col-lg-8">
            <div class="mb-3">
                <label for="title" class="form-label">Titre</label>
                <input type="text" class="form-control <?= isset($errors['title']) ? 'is-invalid' : '' ?>" id="title" name="title" maxlength="255" required value="<?= htmlspecialchars($movie->getTitle() ?? '') ?>">
                <?php if (isset($errors['title'])) { ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['title']) ?></div>
                <?php } ?>
            </div>

            <div class="mb-3">
                <label for="synopsis" class="form-label">Synopsis</label>
                <textarea class="form-control <?= isset($errors['synopsis']) ? 'is-invalid' : '' ?>" id="synopsis" name="synopsis" rows="6" required><?= htmlspecialchars($movie->getSynopsis() ?? '') ?></textarea>
                <?php if (isset($errors['synopsis'])) { ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['synopsis']) ?></div>
                <?php } ?>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="release_date" class="form-label">Date de sortie</label>
                    <input type="date" class="form-control" id="release_date" name="release_date" value="<?= $movie->getReleaseDate()?->format('Y-m-d') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="duration" class="form-label">Durée (heures:minutes)</label>
                    <input type="time" class="form-control" id="duration" name="duration" value="<?= $movie->getDuration()?->format('H:i') ?>">
                </div>
            </div>

            <div class="mb-3">
                <label for="image" class="form-label">Affiche</label>
                <?php if ($movie->getImageName()) { ?>
                    <div class="mb-2">
                        <img src="<?= htmlspecialchars($movie->getImagePath()) ?>" alt="Affiche actuelle" width="100" class="rounded">
                    </div>
                <?php } ?>
                <input type="file" class="form-control <?= isset($errors['file']) ? 'is-invalid' : '' ?>" id="image" name="image" accept="image/jpeg,image/png,image/webp">
                <div class="form-text">JPEG, PNG ou WebP, 2 Mo maximum. Laissez vide pour garder l'affiche actuelle.</div>
                <?php if (isset($errors['file'])) { ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['file']) ?></div>
                <?php } ?>
            </div>
        </div>

        <!-- Colonne de droite : genres et réalisateurs -->
        <div class="col-lg-4">
            <fieldset class="mb-4">
                <legend class="h6">Genres</legend>
                <div class="border rounded p-2 overflow-auto" style="max-height: 220px;">
                    <?php foreach ($genres as $genre) { ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="genres[]" value="<?= $genre->getId() ?>" id="genre-<?= $genre->getId() ?>" <?= in_array($genre->getId(), $selectedGenreIds, true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="genre-<?= $genre->getId() ?>"><?= htmlspecialchars($genre->getName()) ?></label>
                        </div>
                    <?php } ?>
                </div>
            </fieldset>

            <fieldset class="mb-4">
                <legend class="h6">Réalisateurs</legend>
                <div class="border rounded p-2 overflow-auto mb-3" style="max-height: 220px;">
                    <?php foreach ($directors as $director) { ?>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="directors[]" value="<?= $director->getId() ?>" id="director-<?= $director->getId() ?>" <?= in_array($director->getId(), $selectedDirectorIds, true) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="director-<?= $director->getId() ?>"><?= htmlspecialchars($director->getLastName() . ' ' . $director->getFirstName()) ?></label>
                        </div>
                    <?php } ?>
                </div>

                <p class="small mb-2">Réalisateur absent de la liste ? Ajoutez-le :</p>
                <div class="row g-2">
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm <?= isset($errors['new_director']) ? 'is-invalid' : '' ?>" name="new_director_first_name" placeholder="Prénom" aria-label="Prénom du nouveau réalisateur" value="<?= htmlspecialchars($_POST['new_director_first_name'] ?? '') ?>">
                    </div>
                    <div class="col-6">
                        <input type="text" class="form-control form-control-sm <?= isset($errors['new_director']) ? 'is-invalid' : '' ?>" name="new_director_last_name" placeholder="Nom" aria-label="Nom du nouveau réalisateur" value="<?= htmlspecialchars($_POST['new_director_last_name'] ?? '') ?>">
                    </div>
                </div>
                <?php if (isset($errors['new_director'])) { ?>
                    <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['new_director']) ?></div>
                <?php } ?>
            </fieldset>
        </div>
    </div>

    <div class="d-flex gap-2">
        <button type="submit" class="btn btn-primary">Enregistrer</button>
        <a href="/index.php?controller=admin&action=movies" class="btn btn-outline-primary">Annuler</a>
    </div>
</form>

<?php require_once _TEMPLATEPATH_ . '/footer.php'; ?>