<?php

namespace App\Tools;

class FileTools
{
    // Types d'images acceptés : type MIME réel => extension du fichier enregistré
    private const ALLOWED_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    // Taille maximale : 2 Mo
    private const MAX_SIZE = 2 * 1024 * 1024;

    /*
        Enregistre une image envoyée par un formulaire
        Renvoie ['fileName' => nom du fichier enregistré ou null, 'errors' => tableau d'erreurs]
    */
    public static function uploadImage(string $destinationPath, array $file, ?string $oldFileName = null): array
    {
        // Aucun fichier choisi : ce n'est pas une erreur, l'image est facultative
        if (!isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return ['fileName' => null, 'errors' => []];
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            return ['fileName' => null, 'errors' => ['file' => 'Le fichier n\'a pas pu être envoyé (2 Mo maximum)']];
        }

        if ($file['size'] > self::MAX_SIZE) {
            return ['fileName' => null, 'errors' => ['file' => 'L\'image ne doit pas dépasser 2 Mo']];
        }

        // Type réel du fichier, lu dans son contenu (le nom et l'extension peuvent être falsifiés)
        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!isset(self::ALLOWED_TYPES[$mimeType])) {
            return ['fileName' => null, 'errors' => ['file' => 'L\'image doit être au format JPEG, PNG ou WebP']];
        }

        // Nom aléatoire + extension choisie par nous : rien ne vient du nom d'origine
        $fileName = 'movie-' . bin2hex(random_bytes(8)) . '.' . self::ALLOWED_TYPES[$mimeType];

        if (!move_uploaded_file($file['tmp_name'], _ROOTPATH_ . $destinationPath . $fileName)) {
            return ['fileName' => null, 'errors' => ['file' => 'L\'image n\'a pas pu être enregistrée']];
        }

        // La nouvelle image est enregistrée : on supprime l'ancienne
        if ($oldFileName) {
            self::deleteImage($destinationPath, $oldFileName);
        }

        return ['fileName' => $fileName, 'errors' => []];
    }

    /*
        Supprime une image du dossier d'upload
    */
    public static function deleteImage(string $destinationPath, string $fileName): void
    {
        // basename() garde seulement le nom du fichier : impossible de remonter dans les dossiers avec "../"
        $path = _ROOTPATH_ . $destinationPath . basename($fileName);

        if (is_file($path)) {
            unlink($path);
        }
    }
}
