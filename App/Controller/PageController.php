<?php

namespace App\Controller;

use App\Repository\MovieRepository;

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
        Page d'accueil : présentation du site et derniers films ajoutés
        Exemple d'appel depuis l'url : ?controller=page&action=home
    */
    protected function home()
    {
        try {
            $movieRepository = new MovieRepository();

            $this->render('page/home', [
                'latestMovies' => $movieRepository->findLatest(6),
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
