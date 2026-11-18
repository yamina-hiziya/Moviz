<?php require_once _ROOTPATH_ . '/templates/header.php'; ?>

<div class="row flex-lg-row-reverse align-items-center g-5 py-5">
    <div class="col-10 col-sm-8 col-lg-6">
        <img src="/assets/images/logo-moviz.png" class="d-block mx-lg-auto img-fluid" alt="Logo Moviz" width="400" loading="lazy">
    </div>
    <div class="col-lg-6">
        <h1 class="display-5 fw-bold text-body-emphasis lh-1 mb-3">Votre prochaine soirée cinéma commence ici</h1>
        <p class="lead">Parcourez notre sélection de films et trouvez votre prochain coup de cœur.</p>
        <div class="d-grid gap-2 d-md-flex justify-content-md-start">
            <a href="/index.php?controller=movie&action=list" class="btn btn-primary btn-lg px-4 me-md-2">Voir les films</a>
        </div>
    </div>
</div>

<?php require_once _ROOTPATH_ . '/templates/footer.php'; ?>