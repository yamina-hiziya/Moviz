<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\User;
use App\Repository\MovieRepository;
use App\Repository\GenreRepository;
use App\Repository\DirectorRepository;
use App\Repository\ReviewRepository;
use App\Tools\Security;

class MovieController extends Controller
{

    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'show':
                        $this->show();
                        break;
                    case 'list':
                        $this->list();
                        break;
                    case 'delete':
                        // Appeler méthode delete()
                        break;
                    default:
                        throw new \Exception("Cette action n'existe pas : " . $_GET['action']);
                        break;
                }
            } else {
                throw new \Exception("Aucune action détectée");
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function show()
    {
        //cette function va appeler le repository pour récupérer le film en bdd et l'afficher dans la vue
        try {
            if (!isset($_GET['id'])) {
                throw new \Exception("L'id du film est manquant dans l'URL");
            }

            // recuperer le film avec le repository
            $movieRepository = new MovieRepository();
            $movie = $movieRepository->findOneById((int)$_GET['id']);

            if (!$movie) {
                $this->render('errors/default', [
                    'error' => 'Ce film n\'existe pas',
                ]);
                return;
            }

            $reviewRepository = new ReviewRepository();
            $errors = [];

            // Avis déjà donné par l'utilisateur connecté (false s'il n'a pas encore noté)
            $userReview = false;
            if (User::isLogged()) {
                $userReview = $reviewRepository->findOneByUserAndMovie(User::getCurrentUserId(), $movie->getId());
            }

            // Envoi du formulaire d'avis
            if (isset($_POST['saveReview'])) {
                if (!User::isLogged()) {
                    $errors['review_form'] = 'Vous devez être connecté pour noter un film';
                } else if (!Security::checkCsrfToken($_POST['csrf_token'] ?? null)) {
                    $errors['review_form'] = 'Le formulaire a expiré, merci de réessayer';
                } else if ($userReview) {
                    $errors['review_form'] = 'Vous avez déjà noté ce film';
                } else {
                    // Une critique vide est enregistrée comme NULL (elle est optionnelle)
                    $text = trim($_POST['review'] ?? '');

                    $review = new Review();
                    $review->setRate(isset($_POST['rate']) ? (int)$_POST['rate'] : null)
                        ->setReview($text !== '' ? $text : null)
                        ->setApproved(false)
                        ->setUserId(User::getCurrentUserId())
                        ->setMovieId($movie->getId());

                    $errors = $review->validate();

                    if (empty($errors)) {
                        $reviewRepository->persist($review);
                        header('location: index.php?controller=movie&action=show&id=' . $movie->getId() . '&review=sent');
                        exit;
                    }
                }
            }

            $genreRepository = new GenreRepository();
            $genres = $genreRepository->findAllByMovieId($movie->getId());

            $directorRepository = new DirectorRepository();
            $directors = $directorRepository->findAllByMovieId($movie->getId());

            $this->render('movie/show', [
                'movie' => $movie,
                'genres' => $genres,
                'directors' => $directors,
                'reviews' => $reviewRepository->findApprovedByMovieId($movie->getId()),
                'ratingStats' => $reviewRepository->getRatingStatsByMovieId($movie->getId()),
                'userReview' => $userReview,
                'errors' => $errors,
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function list()
    {
        //cette function va appeler le repository pour récupérer le film en bdd et l'afficher dans la vue
        try {

            // recuperer tous les films avec le repository
            $movieRepository = new MovieRepository();
            $movies = $movieRepository->findAll();

            $this->render('movie/list', [
                'movies' => $movies,
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
