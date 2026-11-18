<?php require_once _TEMPLATEPATH_ . '/header.php';

/**
 * @var string[] $errors
 */

?>


<h1>Connexion</h1>

<?php foreach ($errors as $error) { ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($error) ?>
    </div>
<?php } ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= \App\Tools\Security::getCsrfToken() ?>">

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <input type="submit" name="loginUser" class="btn btn-primary" value="Se connecter">
</form>

<?php require_once _TEMPLATEPATH_ . '/footer.php'; ?>