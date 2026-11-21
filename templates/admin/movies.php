<?php require_once _TEMPLATEPATH_ . '/header.php';
/**
 * @var App\Entity\Movie[] $movies
 * @var array|null $views [movie_id => vues], null si MongoDB est indisponible
 * @var string|null $message
 */

// Textes des messages de confirmation (on n'affiche jamais directement le contenu de l'URL)
$messages = [
    'saved' => 'Le film a bien été enregistré.',
    'deleted' => 'Le film a été supprimé, ainsi que ses critiques.',
];
?>

<div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
    <h1 class="mb-0">Gestion des films</h1>
    <div class="d-flex flex-wrap gap-2">
        <a href="/index.php?controller=admin&action=dashboard" class="btn btn-outline-primary">← Administration</a>
        <a href="/index.php?controller=admin&action=movieForm" class="btn btn-primary">+ Ajouter un film</a>
    </div>
</div>

<?php if ($message !== null && isset($messages[$message])) { ?>
    <div class="alert alert-success" role="alert">
        <?= $messages[$message] ?>
    </div>
<?php } ?>

<p class="text-muted"><?= count($movies) ?> film<?= count($movies) > 1 ? 's' : '' ?></p>

<div class="table-responsive">
    <table class="table align-middle">
        <thead>
            <tr>
                <th scope="col">Affiche</th>
                <th scope="col">Titre</th>
                <th scope="col">Sortie</th>
                <th scope="col">Vues</th>
                <th scope="col" class="text-end">Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($movies as $movie) { ?>
                <tr>
                    <td>
                        <img src="<?= htmlspecialchars($movie->getImagePath()) ?>" alt="" width="50" class="rounded">
                    </td>
                    <td>
                        <a href="/index.php?controller=movie&action=show&id=<?= $movie->getId() ?>"><?= htmlspecialchars($movie->getTitle()) ?></a>
                    </td>
                    <td>
                        <?= $movie->getReleaseDate() ? $movie->getReleaseDate()->format('d/m/Y') : 'N/C' ?>
                    </td>
                    <td>
                        <?= $views === null ? '—' : ($views[$movie->getId()] ?? 0) ?>
                    </td>
                    <td class="text-end">
                        <div class="d-inline-flex gap-2">
                            <a href="/index.php?controller=admin&action=movieForm&id=<?= $movie->getId() ?>" class="btn btn-outline-primary btn-sm">Modifier</a>

                            <form method="POST" action="/index.php?controller=admin&action=deleteMovie" onsubmit="return confirm(<?= htmlspecialchars(json_encode('Supprimer définitivement « ' . $movie->getTitle() . ' » et toutes ses critiques ?', JSON_UNESCAPED_UNICODE)) ?>);">
                                <input type="hidden" name="csrf_token" value="<?= \App\Tools\Security::getCsrfToken() ?>">
                                <input type="hidden" name="id" value="<?= $movie->getId() ?>">
                                <button type="submit" class="btn btn-outline-danger btn-sm">Supprimer</button>
                            </form>
                        </div>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php require_once _TEMPLATEPATH_ . '/footer.php'; ?>