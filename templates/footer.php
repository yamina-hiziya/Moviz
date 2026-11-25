</main>



<footer class="py-3 my-4">
    <ul class="nav justify-content-center border-bottom pb-3 mb-3">
        <li class="nav-item"><a href="<?= BASE_URL ?>/" class="nav-link px-2 text-muted">Accueil</a></li>
        <li class="nav-item"><a href="<?= BASE_URL ?>/index.php?controller=movie&action=list" class="nav-link px-2 text-muted">Les films</a></li>
    </ul>
    <p class="text-center text-muted">© <?= date('Y'); ?> - Mooviz</p>
    <div class="text-center text-muted small">
        <a href="https://www.themoviedb.org/" target="_blank" rel="noopener noreferrer">
            <img src="<?= BASE_URL ?>/assets/images/tmdb-logo.svg" alt="The Movie Database (TMDB)" height="12" class="mb-2">
        </a>
        <p class="mb-0">
            Données et affiches de films fournies par The Movie Database (TMDB).
            <br>
            Ce site utilise l'API TMDB mais n'est ni approuvé ni certifié par TMDB.
        </p>
    </div>
</footer>

</div>


</body>

</html>