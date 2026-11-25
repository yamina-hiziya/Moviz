<?php

require_once _ROOTPATH_ . '/templates/header.php';

/**
 * @var App\Entity\Movie[] $movies
 */
?>

<h1 class="mb-4">Les films</h1>

<div class="row">
    <?php foreach ($movies as $movie) { ?>
        <div class="col-md-4 my-2">
            <div class="card w-100 h-100">
                <img src="<?= htmlspecialchars($movie->getImagePath()) ?>" class="card-img-top" alt="<?= htmlspecialchars($movie->getTitle()) ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($movie->getTitle()) ?></h5>
                    <a href="<?= BASE_URL ?>/index.php?controller=movie&action=show&id=<?= $movie->getId() ?>" class="btn btn-primary">Voir la fiche</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>