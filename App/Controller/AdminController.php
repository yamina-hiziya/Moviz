<?php

namespace App\Controller;

use App\Db\Mysql;
use App\Entity\Movie;
use App\Entity\User;
use App\Repository\DirectorRepository;
use App\Repository\GenreRepository;
use App\Repository\MovieRepository;
use App\Repository\ReviewRepository;
use App\Repository\MovieViewRepository;
use App\Tools\FileTools;
use App\Tools\Security;

class AdminController extends Controller
{
    public function route(): void
    {
        try {
            // Contrôle d'accès : toutes les actions de ce contrôleur sont réservées aux administrateurs
            if (!User::isAdmin()) {
                http_response_code(403);
                throw new \Exception("Accès refusé : cette page est réservée aux administrateurs");
            }

            // Sans action dans l'URL, on affiche le tableau de bord
            $action = $_GET['action'] ?? 'dashboard';

            switch ($action) {
                case 'dashboard':
                    $this->dashboard();
                    break;
                case 'approveReview':
                    $this->approveReview();
                    break;
                case 'deleteReview':
                    $this->deleteReview();
                    break;
                case 'movies':
                    $this->movies();
                    break;
                case 'movieForm':
                    $this->movieForm();
                    break;
                case 'deleteMovie':
                    $this->deleteMovie();
                    break;
                default:
                    throw new \Exception("Cette action n'existe pas : " . $action);
                    break;
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $this->errorMessage($e)
            ]);
        }
    }

    // ---------------------------------------------------------------
    // Critiques
    // ---------------------------------------------------------------

    /*
        Tableau de bord : liste des critiques en attente de validation
    */
    protected function dashboard()
    {
        $reviewRepository = new ReviewRepository();

        $this->render('admin/dashboard', [
            'pendingReviews' => $reviewRepository->findPending(),
            // Message de confirmation après une action (approved ou deleted)
            'message' => $_GET['message'] ?? null,
        ]);
    }

    /*
        Valide une critique
    */
    protected function approveReview()
    {
        $id = $this->getIdFromPost();

        $reviewRepository = new ReviewRepository();
        $reviewRepository->approve($id);

        header('location: index.php?controller=admin&action=dashboard&message=approved');
        exit;
    }

    /*
        Supprime une critique
    */
    protected function deleteReview()
    {
        $id = $this->getIdFromPost();

        $reviewRepository = new ReviewRepository();
        $reviewRepository->delete($id);

        header('location: index.php?controller=admin&action=dashboard&message=deleted');
        exit;
    }

    // ---------------------------------------------------------------
    // Films
    // ---------------------------------------------------------------

    /*
    Liste de tous les films
*/
    protected function movies()
    {
        $movieRepository = new MovieRepository();
        $movies = $movieRepository->findAll();

        // Nombre de vues de chaque film (MongoDB), en une seule requête
        // null si MongoDB est indisponible : la colonne affichera alors un tiret
        try {
            $movieViewRepository = new MovieViewRepository();
            $views = $movieViewRepository->getViewsForMovies(array_map(fn($movie) => $movie->getId(), $movies));
        } catch (\Exception $e) {
            $views = null;
        }

        $this->render('admin/movies', [
            'movies' => $movies,
            'views' => $views,
            // Message de confirmation après une action (saved ou deleted)
            'message' => $_GET['message'] ?? null,
        ]);
    }

    /*
        Formulaire d'ajout (sans id dans l'URL) ou de modification (avec id) d'un film
    */
    protected function movieForm()
    {
        $movieRepository = new MovieRepository();
        $genreRepository = new GenreRepository();
        $directorRepository = new DirectorRepository();

        $movie = new Movie();
        $selectedGenreIds = [];
        $selectedDirectorIds = [];
        $errors = [];

        // Modification : on charge le film existant et ses genres / réalisateurs actuels
        if (isset($_GET['id'])) {
            $movie = $movieRepository->findOneById((int)$_GET['id']);
            if (!$movie) {
                throw new \Exception("Ce film n'existe pas");
            }
            $selectedGenreIds = array_map(fn($genre) => $genre->getId(), $genreRepository->findAllByMovieId($movie->getId()));
            $selectedDirectorIds = array_map(fn($director) => $director->getId(), $directorRepository->findAllByMovieId($movie->getId()));
        }

        // Envoi du formulaire
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->checkPostRequest();

            $movie->setTitle(trim($_POST['title'] ?? ''))
                ->setSynopsis(trim($_POST['synopsis'] ?? ''))
                ->setReleaseDate($this->toDate($_POST['release_date'] ?? ''))
                ->setDuration($this->toTime($_POST['duration'] ?? ''));

            // Ids des cases cochées : (array) et intval garantissent qu'on n'a que des nombres
            $selectedGenreIds = array_filter(array_map('intval', (array)($_POST['genres'] ?? [])));
            $selectedDirectorIds = array_filter(array_map('intval', (array)($_POST['directors'] ?? [])));

            $errors = $movie->validate();

            // Nouveau réalisateur (facultatif) : il faut le prénom ET le nom
            $newFirstName = trim($_POST['new_director_first_name'] ?? '');
            $newLastName = trim($_POST['new_director_last_name'] ?? '');
            if (($newFirstName === '') !== ($newLastName === '')) {
                $errors['new_director'] = 'Renseignez le prénom et le nom du nouveau réalisateur';
            }

            // L'image n'est envoyée que si le reste du formulaire est valide
            if (empty($errors)) {
                $upload = FileTools::uploadImage(MOVIES_IMAGES_FOLDER, $_FILES['image'] ?? [], $movie->getImageName());
                if (!empty($upload['errors'])) {
                    $errors = array_merge($errors, $upload['errors']);
                } else if ($upload['fileName']) {
                    $movie->setImageName($upload['fileName']);
                }
            }

            if (empty($errors)) {
                // Transaction : le film et tous ses liens sont enregistrés, ou rien
                $pdo = Mysql::getInstance()->getPDO();
                $pdo->beginTransaction();

                try {
                    if ($movie->getId() === null) {
                        $movieId = $movieRepository->insert($movie);
                    } else {
                        $movieRepository->update($movie);
                        $movieId = $movie->getId();
                    }

                    // Tables de liaison : on retire tout, puis on remet ce qui est coché
                    $genreRepository->unlinkAllFromMovie($movieId);
                    foreach (array_unique($selectedGenreIds) as $genreId) {
                        $genreRepository->linkToMovie($movieId, $genreId);
                    }

                    if ($newFirstName !== '' && $newLastName !== '') {
                        $selectedDirectorIds[] = $directorRepository->findOrCreate($newFirstName, $newLastName);
                    }
                    $directorRepository->unlinkAllFromMovie($movieId);
                    foreach (array_unique($selectedDirectorIds) as $directorId) {
                        $directorRepository->linkToMovie($movieId, $directorId);
                    }

                    $pdo->commit();
                } catch (\Exception $e) {
                    $pdo->rollBack();
                    throw $e;
                }

                header('location: index.php?controller=admin&action=movies&message=saved');
                exit;
            }
        }

        $this->render('admin/movie_form', [
            'movie' => $movie,
            'genres' => $genreRepository->findAll(),
            'directors' => $directorRepository->findAll(),
            'selectedGenreIds' => $selectedGenreIds,
            'selectedDirectorIds' => $selectedDirectorIds,
            'errors' => $errors,
            'pageTitle' => $movie->getId() === null ? 'Ajouter un film' : 'Modifier « ' . $movie->getTitle() . ' »',
        ]);
    }

    /*
    Supprime un film, ses liens, ses critiques, son affiche et ses statistiques de vues
*/
    protected function deleteMovie()
    {
        $id = $this->getIdFromPost();

        $movieRepository = new MovieRepository();
        $movie = $movieRepository->findOneById($id);
        if (!$movie) {
            throw new \Exception("Ce film n'existe pas");
        }

        $pdo = Mysql::getInstance()->getPDO();
        $pdo->beginTransaction();

        try {
            $movieRepository->delete($id);
            $pdo->commit();
        } catch (\Exception $e) {
            $pdo->rollBack();
            throw $e;
        }

        // L'affiche n'est supprimée qu'une fois le film bien supprimé en base
        if ($movie->getImageName()) {
            FileTools::deleteImage(MOVIES_IMAGES_FOLDER, $movie->getImageName());
        }

        // Statistiques de vues du film (MongoDB) : non critiques, une erreur ne bloque pas la suppression
        try {
            $movieViewRepository = new MovieViewRepository();
            $movieViewRepository->deleteByMovieId($id);
        } catch (\Exception $e) {
            // MongoDB indisponible : les statistiques orphelines sont ignorées à l'affichage
        }

        header('location: index.php?controller=admin&action=movies&message=deleted');
        exit;
    }

    // ---------------------------------------------------------------
    // Outils communs
    // ---------------------------------------------------------------

    /*
        Vérifications communes aux actions qui modifient des données :
        la requête doit être en POST et le jeton CSRF valide
    */
    private function checkPostRequest(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception("Cette action doit être envoyée par un formulaire");
        }
        if (!Security::checkCsrfToken($_POST['csrf_token'] ?? null)) {
            throw new \Exception("Le formulaire a expiré, merci de réessayer");
        }
    }

    /*
        Vérifie la requête puis renvoie l'id envoyé par le formulaire
    */
    private function getIdFromPost(): int
    {
        $this->checkPostRequest();

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new \Exception("Élément introuvable");
        }

        return $id;
    }

    /*
        Convertit "2010-07-16" (champ date) en DateTime, ou null si vide ou invalide
    */
    private function toDate(string $value): ?\DateTime
    {
        $date = \DateTime::createFromFormat('!Y-m-d', $value);
        return $date ?: null;
    }

    /*
        Convertit "02:28" (champ time) en DateTime, ou null si vide ou invalide
    */
    private function toTime(string $value): ?\DateTime
    {
        $time = \DateTime::createFromFormat('!H:i', $value);
        return $time ?: null;
    }
}
