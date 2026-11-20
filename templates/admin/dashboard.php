<?php require_once _TEMPLATEPATH_ . '/header.php';
/**
 * @var App\Entity\Review[] $pendingReviews
 * @var string|null $message
 */

// Textes des messages de confirmation (on n'affiche jamais directement le contenu de l'URL)
$messages = [
    'approved' => 'La critique a été validée : elle est maintenant visible sur la fiche du film.',
    'deleted' => 'La critique a été supprimée.',
];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Administration</h1>
    <a href="/index.php?controller=admin&action=movies" class="btn btn-primary">Gérer les films</a>
</div>

<?php if ($message !== null && isset($messages[$message])) { ?>
    <div class="alert alert-success" role="alert">
        <?= $messages[$message] ?>
    </div>
<?php } ?>

<section class="mb-5">
    <h2 class="h4 mb-3">
        Critiques en attente de validation
        <span class="badge bg-primary"><?= count($pendingReviews) ?></span>
    </h2>

    <?php if (empty($pendingReviews)) { ?>
        <p class="text-muted">Aucune critique en attente. Tout est à jour !</p>
    <?php } else { ?>
        <?php foreach ($pendingReviews as $review) { ?>
            <div class="card mb-3">
                <div class="card-body">
                    <div class="d-flex flex-wrap justify-content-between align-items-start gap-2 mb-2">
                        <div>
                            <a href="/index.php?controller=movie&action=show&id=<?= $review->getMovieId() ?>" class="fw-semibold">
                                <?= htmlspecialchars($review->getMovieTitle()) ?>
                            </a>
                            <span class="text-muted">— par <?= htmlspecialchars($review->getUserFirstName()) ?></span>
                        </div>
                        <div>
                            <?php for ($i = 1; $i <= 5; $i++) { ?>
                                <span class="<?= $i <= $review->getRate() ? 'text-warning' : 'text-secondary' ?>">★</span>
                            <?php } ?>
                        </div>
                    </div>

                    <p class="card-text"><?= nl2br(htmlspecialchars($review->getReview())) ?></p>

                    <div class="d-flex gap-2">
                        <form method="POST" action="/index.php?controller=admin&action=approveReview">
                            <input type="hidden" name="csrf_token" value="<?= \App\Tools\Security::getCsrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $review->getId() ?>">
                            <button type="submit" class="btn btn-primary btn-sm">Valider</button>
                        </form>

                        <form method="POST" action="/index.php?controller=admin&action=deleteReview" onsubmit="return confirm('Supprimer définitivement cette critique et sa note ?');">
                            <input type="hidden" name="csrf_token" value="<?= \App\Tools\Security::getCsrfToken() ?>">
                            <input type="hidden" name="id" value="<?= $review->getId() ?>">
                            <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                        </form>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } ?>
</section>

<?php require_once _TEMPLATEPATH_ . '/footer.php'; ?>