<?php

namespace App\Controller;

use App\Repository\MovieRepository;
use App\Repository\MovieViewRepository;

class PageController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'home':
                        $this->home();
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

    /*
        Page d'accueil : présentation du site, films les plus consultés et derniers films ajoutés
        Exemple d'appel depuis l'url : ?controller=page&action=home
    */
    protected function home()
    {
        try {
            $movieRepository = new MovieRepository();

            // Films les plus consultés : les ids et les vues viennent de MongoDB, les films de MySQL
            // Si MongoDB est indisponible, la section est simplement masquée
            $mostViewedMovies = [];
            try {
                $movieViewRepository = new MovieViewRepository();
                foreach ($movieViewRepository->findMostViewed(6) as $movieId => $views) {
                    $movie = $movieRepository->findOneById($movieId);
                    // Un film supprimé peut encore avoir des statistiques : on l'ignore
                    if ($movie) {
                        $mostViewedMovies[] = ['movie' => $movie, 'views' => $views];
                    }
                }
            } catch (\Exception $e) {
                $mostViewedMovies = [];
            }

            $this->render('page/home', [
                'mostViewedMovies' => $mostViewedMovies,
                'latestMovies' => $movieRepository->findLatest(6),
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
