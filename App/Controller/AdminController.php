<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\ReviewRepository;
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
                default:
                    throw new \Exception("Cette action n'existe pas : " . $action);
                    break;
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

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
        $id = $this->getReviewIdFromPost();

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
        $id = $this->getReviewIdFromPost();

        $reviewRepository = new ReviewRepository();
        $reviewRepository->delete($id);

        header('location: index.php?controller=admin&action=dashboard&message=deleted');
        exit;
    }

    /*
        Vérifications communes aux actions qui modifient des données :
        - la requête doit être en POST (jamais un simple lien)
        - le jeton CSRF doit être valide
        - l'id doit être un nombre positif
    */
    private function getReviewIdFromPost(): int
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            throw new \Exception("Cette action doit être envoyée par un formulaire");
        }
        if (!Security::checkCsrfToken($_POST['csrf_token'] ?? null)) {
            throw new \Exception("Le formulaire a expiré, merci de réessayer");
        }

        $id = (int)($_POST['id'] ?? 0);
        if ($id <= 0) {
            throw new \Exception("Critique introuvable");
        }

        return $id;
    }
}
