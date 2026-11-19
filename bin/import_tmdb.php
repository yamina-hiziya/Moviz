<?php

/*
    Import des films populaires depuis l'API TMDB (The Movie Database)

    À lancer dans le terminal, depuis le dossier du projet :
    docker exec -w /var/www/html moviz-web-1 php bin/import_tmdb.php
*/

use App\Db\Mysql;
use App\Entity\Movie;
use App\Repository\MovieRepository;
use App\Repository\GenreRepository;
use App\Repository\DirectorRepository;

// ---------------------------------------------------------------
// 1. Sécurité : ce script ne s'exécute que dans le terminal
// ---------------------------------------------------------------
// PHP_SAPI vaut "cli" en ligne de commande, et "apache2handler" quand la page est ouverte dans un navigateur
if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    exit('Ce script doit être lancé dans le terminal.');
}

// ---------------------------------------------------------------
// 2. Initialisation (comme dans index.php)
// ---------------------------------------------------------------
// dirname(__DIR__) = le dossier parent de bin/, c'est-à-dire la racine du projet
define('_ROOTPATH_', dirname(__DIR__));
require_once _ROOTPATH_ . '/config.php';

spl_autoload_register(function (string $class) {
    $file = _ROOTPATH_ . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require_once $file;
    }
});

$config = require _ROOTPATH_ . '/tmdb_config.php';

// ---------------------------------------------------------------
// 3. Fonctions utilitaires
// ---------------------------------------------------------------

/*
    Appelle l'API TMDB et renvoie la réponse JSON sous forme de tableau PHP
*/
function tmdbGet(array $config, string $path, array $params = []): array
{
    $params['language'] = $config['language'];
    // http_build_query transforme ['page' => 1, 'language' => 'fr-FR'] en "page=1&language=fr-FR"
    $url = $config['api_url'] . $path . '?' . http_build_query($params);

    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true, // renvoie la réponse au lieu de l'afficher
        CURLOPT_TIMEOUT => 15,          // abandonne au bout de 15 secondes
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . $config['token'],
            'Accept: application/json',
        ],
    ]);
    $response = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false) {
        throw new \Exception('Erreur réseau : ' . curl_error($ch));
    }
    if ($status !== 200) {
        throw new \Exception("L'API TMDB a répondu avec le code $status pour $path");
    }

    // true = tableau associatif ; JSON_THROW_ON_ERROR = exception si le JSON est invalide
    return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
}

/*
    Télécharge l'affiche dans uploads/movies/ et renvoie le nom du fichier (null si pas d'affiche)
*/
function downloadPoster(array $config, ?string $posterPath): ?string
{
    // TMDB renvoie un chemin du type "/qJ2tW6WMUDux911r6m7haRef0WH.jpg"
    // On vérifie son format pour ne jamais écrire un nom de fichier inattendu sur le disque
    if (!$posterPath || !preg_match('#^/[A-Za-z0-9]+\.(jpg|png)$#', $posterPath)) {
        return null;
    }

    $fileName = 'tmdb-' . ltrim($posterPath, '/');
    $destination = _ROOTPATH_ . MOVIES_IMAGES_FOLDER . $fileName;

    // Si l'affiche a déjà été téléchargée, on ne la retélécharge pas
    if (is_file($destination)) {
        return $fileName;
    }

    $ch = curl_init($config['image_url'] . $posterPath);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
    ]);
    $image = curl_exec($ch);
    $status = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($image === false || $status !== 200) {
        return null;
    }

    file_put_contents($destination, $image);
    return $fileName;
}

/*
    Découpe un nom complet en prénom + nom
    "Christopher Nolan" => ["Christopher", "Nolan"]
    "Denis Villeneuve"  => ["Denis", "Villeneuve"]
*/
function splitName(string $fullName): array
{
    // explode avec la limite 2 : on coupe seulement au premier espace
    $parts = explode(' ', trim($fullName), 2);
    return [$parts[0], $parts[1] ?? ''];
}

// ---------------------------------------------------------------
// 4. Import
// ---------------------------------------------------------------
$pdo = Mysql::getInstance()->getPDO();
// En cas d'erreur SQL, PDO lance une exception (nécessaire pour annuler la transaction)
$pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

$movieRepository = new MovieRepository();
$genreRepository = new GenreRepository();
$directorRepository = new DirectorRepository();

$imported = 0;
$skipped = 0;
$failed = 0;

for ($page = 1; $page <= $config['pages']; $page++) {
    echo "--- Page $page ---" . PHP_EOL;

    try {
        $list = tmdbGet($config, '/movie/popular', ['page' => $page]);
    } catch (\Exception $e) {
        echo 'Impossible de récupérer la liste : ' . $e->getMessage() . PHP_EOL;
        break;
    }

    foreach ($list['results'] as $result) {
        $tmdbId = (int)$result['id'];

        // Film déjà importé lors d'un précédent lancement : on passe au suivant
        if ($movieRepository->existsByTmdbId($tmdbId)) {
            echo 'Déjà présent : ' . $result['title'] . PHP_EOL;
            $skipped++;
            continue;
        }

        try {
            // Détail du film + "credits" (acteurs et équipe technique) en un seul appel
            $details = tmdbGet($config, '/movie/' . $tmdbId, ['append_to_response' => 'credits']);
            $imageName = downloadPoster($config, $details['poster_path'] ?? null);

            // La durée TMDB est en minutes (ex : 148) : on la convertit en heure "02:28:00"
            $duration = null;
            if (!empty($details['runtime'])) {
                $duration = new \DateTime(sprintf('%02d:%02d:00', intdiv($details['runtime'], 60), $details['runtime'] % 60));
            }

            $movie = new Movie();
            $movie->setTitle($details['title'])
                ->setSynopsis($details['overview'] !== '' ? $details['overview'] : 'Synopsis non disponible.')
                ->setReleaseDate(!empty($details['release_date']) ? new \DateTime($details['release_date']) : null)
                ->setDuration($duration)
                ->setImageName($imageName);

            // --- Début de la transaction : tout ou rien pour ce film ---
            $pdo->beginTransaction();

            $movieId = $movieRepository->insert($movie, $tmdbId);

            // Genres
            foreach ($details['genres'] as $genre) {
                $genreId = $genreRepository->findOrCreateByName($genre['name']);
                $genreRepository->linkToMovie($movieId, $genreId);
            }

            // Réalisateurs : dans l'équipe technique, ceux dont le poste est "Director"
            $linkedDirectors = [];
            foreach ($details['credits']['crew'] as $member) {
                if ($member['job'] !== 'Director') {
                    continue;
                }
                [$firstName, $lastName] = splitName($member['name']);
                $directorId = $directorRepository->findOrCreate($firstName, $lastName);

                // Sécurité : ne pas relier deux fois le même réalisateur au même film
                if (!in_array($directorId, $linkedDirectors, true)) {
                    $directorRepository->linkToMovie($movieId, $directorId);
                    $linkedDirectors[] = $directorId;
                }
            }

            $pdo->commit();
            // --- Fin de la transaction : le film est enregistré ---

            echo 'Importé : ' . $details['title'] . PHP_EOL;
            $imported++;
        } catch (\Throwable $e) {
            // Une erreur pendant la transaction : on annule tout ce qui a été fait pour ce film
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            echo 'Erreur pour ' . $result['title'] . ' : ' . $e->getMessage() . PHP_EOL;
            $failed++;
        }
    }
}

echo PHP_EOL . "Terminé : $imported importé(s), $skipped déjà présent(s), $failed erreur(s)." . PHP_EOL;
