<?php require_once _TEMPLATEPATH_ . '/header.php';
/**
 * @var \App\Entity\User $user
 * @var string $pageTitle
 * @var array $errors
 */

?>

<h1><?= htmlspecialchars($pageTitle) ?></h1>

<?php if (isset($errors['csrf'])) { ?>
    <div class="alert alert-danger" role="alert">
        <?= htmlspecialchars($errors['csrf']) ?>
    </div>
<?php } ?>

<form method="POST">
    <input type="hidden" name="csrf_token" value="<?= \App\Tools\Security::getCsrfToken() ?>">

    <div class="mb-3">
        <label for="first_name" class="form-label">Prénom</label>
        <input type="text" class="form-control <?= isset($errors['first_name']) ? 'is-invalid' : '' ?>" id="first_name" name="first_name" value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>">
        <?php if (isset($errors['first_name'])) { ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['first_name']) ?></div>
        <?php } ?>
    </div>
    <div class="mb-3">
        <label for="last_name" class="form-label">Nom</label>
        <input type="text" class="form-control <?= isset($errors['last_name']) ? 'is-invalid' : '' ?>" id="last_name" name="last_name" value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>">
        <?php if (isset($errors['last_name'])) { ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['last_name']) ?></div>
        <?php } ?>
    </div>
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control <?= isset($errors['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
        <?php if (isset($errors['email'])) { ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['email']) ?></div>
        <?php } ?>
    </div>
    <div class="mb-3">
        <label for="password" class="form-label">Mot de passe</label>
        <input type="password" class="form-control <?= isset($errors['password']) ? 'is-invalid' : '' ?>" id="password" name="password" value="">
        <?php if (isset($errors['password'])) { ?>
            <div class="invalid-feedback"><?= htmlspecialchars($errors['password']) ?></div>
        <?php } ?>
    </div>

    <input type="submit" name="saveUser" class="btn btn-primary" value="Enregistrer">

</form>


<?php require_once _TEMPLATEPATH_ . '/footer.php'; ?>