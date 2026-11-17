<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\GenreRepository;
use App\Repository\DirectorRepository;

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
                        // Appeler méthode list()
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
            if (isset($_GET['id'])) {
                // recuperer le film avec le repository
                $movieRepository = new MovieRepository();
                $id = (int)$_GET['id'];
                $movie = $movieRepository->findOneById($id);

                if ($movie) {
                    $genreRepository = new GenreRepository();
                    $genres = $genreRepository->findAllByMovieId($movie->getId());

                    $directorRepository = new DirectorRepository();
                    $directors = $directorRepository->findAllByMovieId($movie->getId());

                    $this->render('movie/show', [
                        'movie' => $movie,
                        'genres' => $genres,
                        'directors' => $directors,
                    ]);
                } else {
                    $this->render('errors/default', [
                        'error' => 'Ce film n\'existe pas',
                    ]);
                }
            } else {
                throw new \Exception("id est manquand en parametre d'url");
            }
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
