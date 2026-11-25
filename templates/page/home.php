<?php require_once _ROOTPATH_ . '/templates/header.php';
/**
 * @var array $mostViewedMovies Tableau de ['movie' => Movie, 'views' => int]
 * @var App\Entity\Movie[] $latestMovies
 */
?>

<!-- Bandeau d'accueil -->
<section class="bg-primary text-white rounded-4 p-4 p-md-5 mb-5">
    <div class="row align-items-center">
        <div class="col-lg-8">
            <h1 class="display-5 fw-bold text-white mb-3">Votre prochaine soirée cinéma commence ici</h1>
            <p class="lead mb-4">
                Parcourez notre sélection de films, découvrez les avis de la communauté
                et partagez votre propre note <span class="text-warning">★</span>
            </p>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= BASE_URL ?>/index.php?controller=movie&action=list" class="btn btn-light btn-lg px-4">Voir les films</a>
                <?php if (!\App\Entity\User::isLogged()) { ?>
                    <a href="<?= BASE_URL ?>/index.php?controller=user&action=register" class="btn btn-outline-light btn-lg px-4">Créer un compte</a>
                <?php } ?>
            </div>
        </div>
    </div>
</section>

<!-- Films les plus consultés (statistiques MongoDB) : affichée seulement s'il y a des vues -->
<?php if (!empty($mostViewedMovies)) { ?>
    <section class="mb-5">
        <h2 class="h3 mb-3">Les plus consultés</h2>

        <div class="row g-3">
            <?php foreach ($mostViewedMovies as $item) { ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?= BASE_URL ?>/index.php?controller=movie&action=show&id=<?= $item['movie']->getId() ?>" class="text-decoration-none">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($item['movie']->getImagePath()) ?>" class="card-img-top" alt="<?= htmlspecialchars($item['movie']->getTitle()) ?>" loading="lazy">
                            <div class="card-body p-2">
                                <p class="card-title small fw-semibold mb-1"><?= htmlspecialchars($item['movie']->getTitle()) ?></p>
                                <p class="small text-muted mb-0"><?= $item['views'] ?> vue<?= $item['views'] > 1 ? 's' : '' ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    </section>
<?php } ?>

<!-- Derniers films ajoutés -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="h3 mb-0">Les derniers films ajoutés</h2>
        <a href="<?= BASE_URL ?>/index.php?controller=movie&action=list">Tous les films →</a>
    </div>

    <?php if (empty($latestMovies)) { ?>
        <p class="text-muted">Aucun film pour le moment.</p>
    <?php } else { ?>
        <div class="row g-3">
            <?php foreach ($latestMovies as $movie) { ?>
                <div class="col-6 col-md-4 col-lg-2">
                    <a href="<?= BASE_URL ?>/index.php?controller=movie&action=show&id=<?= $movie->getId() ?>" class="text-decoration-none">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($movie->getImagePath()) ?>" class="card-img-top" alt="<?= htmlspecialchars($movie->getTitle()) ?>" loading="lazy">
                            <div class="card-body p-2">
                                <p class="card-title small fw-semibold mb-0"><?= htmlspecialchars($movie->getTitle()) ?></p>
                            </div>
                        </div>
                    </a>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
</section>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>