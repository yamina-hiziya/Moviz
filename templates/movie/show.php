<?php require_once _ROOTPATH_ . '/templates/header.php';
/**
 * @var App\Entity\Movie $movie
 * @var App\Entity\Genre[] $genres
 * @var App\Entity\Director[] $directors
 * @var App\Entity\Review[] $reviews
 * @var array $ratingStats
 * @var App\Entity\Review|bool $userReview
 * @var array $errors
 */
?>

<h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3 text-center"><?= htmlspecialchars($movie->getTitle()) ?></h1>

<!-- Moyenne des notes -->
<p class="text-center fs-5">
    <?php if ($ratingStats['average'] === null) { ?>
        <span class="text-muted">Pas encore de note</span>
    <?php } else { ?>
        <?php for ($i = 1; $i <= 5; $i++) { ?>
            <span class="<?= $i <= round($ratingStats['average']) ? 'text-warning' : 'text-secondary' ?>">★</span>
        <?php } ?>
        <strong><?= number_format($ratingStats['average'], 1, ',', '') ?>/5</strong>
        <span class="text-muted">(<?= $ratingStats['total'] ?> note<?= $ratingStats['total'] > 1 ? 's' : '' ?>)</span>
    <?php } ?>
</p>

<div class="row">
    <div class="col">
        Date de sortie :
        <?php if ($movie->getReleaseDate()) {
            echo $movie->getReleaseDate()->format('d/m/Y');
        } else {
            echo "N/C";
        } ?>
    </div>
    <div class="col">
        Durée : <?php if ($movie->getDuration()) {
                    echo $movie->getDuration()->format('H\hi');
                } else {
                    echo "N/C";
                } ?>
    </div>
    <div class="col">
        Genre : <?php foreach ($genres as $genre) {
                    /** @var \App\Entity\Genre $genre */
                ?>
            <span><?= htmlspecialchars($genre->getName()) ?></span>
        <?php } ?>
    </div>
    <div class="col">
        Réalisateur : <?php foreach ($directors as $director) {
                            /** @var \App\Entity\Director $director */
                        ?>
            <span><?= htmlspecialchars($director->getFirstName() . ' ' . $director->getLastName()) ?></span>
        <?php } ?>
    </div>
</div>

<div class="row align-items-start g-5 py-5">
    <div class="col-lg-8">
        <p class="lead"><?= nl2br(htmlspecialchars($movie->getSynopsis())) ?></p>
    </div>
    <div class="col-10 col-sm-8 col-lg-4">
        <img src="<?= htmlspecialchars($movie->getImagePath()) ?>" class="d-block mx-lg-auto img-fluid" alt="<?= htmlspecialchars($movie->getTitle()) ?>" width="700" height="500" loading="lazy">
    </div>
</div>

<!-- Votre avis -->
<section class="mb-5">
    <h2 class="h4 mb-3">Votre avis</h2>

    <?php if (isset($_GET['review']) && $_GET['review'] === 'sent') { ?>
        <div class="alert alert-success" role="alert">
            Merci ! Votre note est prise en compte. Votre critique sera visible après validation par un administrateur.
        </div>
    <?php } ?>

    <?php if (isset($errors['review_form'])) { ?>
        <div class="alert alert-danger" role="alert">
            <?= htmlspecialchars($errors['review_form']) ?>
        </div>
    <?php } ?>

    <?php if (!\App\Entity\User::isLogged()) { ?>

        <p>
            <a href="/index.php?controller=auth&action=login">Connectez-vous</a> pour noter ce film et donner votre avis.
        </p>

    <?php } else if ($userReview) { ?>

        <p>
            Vous avez donné la note de <strong><?= $userReview->getRate() ?>/5</strong> à ce film.
            <?php if ($userReview->getReview() && !$userReview->isApproved()) { ?>
                <br><span class="text-muted">Votre critique est en attente de validation.</span>
            <?php } ?>
        </p>

    <?php } else { ?>

        <form method="POST">
            <input type="hidden" name="csrf_token" value="<?= \App\Tools\Security::getCsrfToken() ?>">

            <div class="mb-3">
                <label class="form-label d-block">Votre note</label>
                <!-- Étoiles de 5 à 1 : le CSS les affiche dans le bon ordre grâce au float: right -->
                <div class="rate enabled">
                    <?php for ($i = 5; $i >= 1; $i--) { ?>
                        <input type="radio" id="star<?= $i ?>" name="rate" value="<?= $i ?>" <?= (isset($_POST['rate']) && (int)$_POST['rate'] === $i) ? 'checked' : '' ?>>
                        <label for="star<?= $i ?>" title="<?= $i ?> étoile<?= $i > 1 ? 's' : '' ?>"><?= $i ?> étoile<?= $i > 1 ? 's' : '' ?></label>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
                <?php if (isset($errors['rate'])) { ?>
                    <div class="invalid-feedback d-block"><?= htmlspecialchars($errors['rate']) ?></div>
                <?php } ?>
            </div>

            <div class="mb-3">
                <label for="review" class="form-label">Votre critique (facultative)</label>
                <textarea class="form-control <?= isset($errors['review']) ? 'is-invalid' : '' ?>" id="review" name="review" rows="4" maxlength="2000"><?= htmlspecialchars($_POST['review'] ?? '') ?></textarea>
                <?php if (isset($errors['review'])) { ?>
                    <div class="invalid-feedback"><?= htmlspecialchars($errors['review']) ?></div>
                <?php } ?>
            </div>

            <input type="submit" name="saveReview" class="btn btn-primary" value="Envoyer mon avis">
        </form>

    <?php } ?>
</section>

<!-- Critiques validées -->
<section class="mb-5">
    <h2 class="h4 mb-3">Les critiques</h2>

    <?php if (empty($reviews)) { ?>
        <p class="text-muted">Aucune critique pour le moment.</p>
    <?php } else { ?>
        <?php foreach ($reviews as $review) { ?>
            <div class="card mb-3">
                <div class="card-body">
                    <p class="mb-1">
                        <?php for ($i = 1; $i <= 5; $i++) { ?>
                            <span class="<?= $i <= $review->getRate() ? 'text-warning' : 'text-secondary' ?>">★</span>
                        <?php } ?>
                        <strong class="ms-2"><?= htmlspecialchars($review->getUserFirstName() ?? 'Anonyme') ?></strong>
                    </p>
                    <?php if ($review->getReview()) { ?>
                        <p class="card-text"><?= nl2br(htmlspecialchars($review->getReview())) ?></p>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</section>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>