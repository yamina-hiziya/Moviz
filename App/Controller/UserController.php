<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Entity\User;
use App\Tools\Security;


class UserController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'register':
                        $this->register();
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
                'error' => $this->errorMessage($e)
            ]);
        }
    }

    protected function register()
    {
        try {
            $errors = [];
            $user = new User();

            if (isset($_POST['saveUser'])) {

                // Vérifie que le formulaire vient bien de notre site
                if (!Security::checkCsrfToken($_POST['csrf_token'] ?? null)) {
                    $errors['csrf'] = 'Le formulaire a expiré, merci de réessayer';
                } else {
                    // gérer l'inscription utilisateur
                    $user->hydrate($_POST);
                    $user->setRole(ROLE_USER);
                    $errors = $user->validate();

                    $userRepository = new UserRepository();

                    // Vérifie que l'email n'est pas déjà utilisé par un autre compte
                    if (!isset($errors['email']) && $userRepository->findOneByEmail($user->getEmail())) {
                        $errors['email'] = 'Un compte existe déjà avec cet email';
                    }

                    if (empty($errors)) {
                        //save en bdd
                        $userRepository->persist($user);
                        header('location: index.php?controller=auth&action=login');
                        exit;
                    }
                }
            }

            $this->render('user/add_edit', [
                'user' => '',
                'pageTitle' => 'Inscription',
                'errors' => $errors
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $this->errorMessage($e)
            ]);
        }
    }
}
