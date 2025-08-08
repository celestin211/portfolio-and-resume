<?php

// src/Service/ImageCacheService.php
namespace App\Service;

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;

class ImageCacheService
{
    private $uploadDir;

    public function __construct(FilesystemAdapter $uploadDir)
    {
        $this->uploadDir = $uploadDir;
    }

    public function getImage(string $imagePath): Response
    {
        $cacheKey = 'image_' . md5($imagePath);  // Utiliser un hash pour le nom du fichier
        $cachedImage = $this->uploadDir->getItem($cacheKey);

        if (!$cachedImage->isHit()) {
            // Si l'image n'est pas en cache, on la charge et l'enregistre dans le cache
            $imageData = file_get_contents($imagePath);  // Charger l'image depuis le système de fichiers
            $cachedImage->set($imageData);
            $this->uploadDir->save($cachedImage);
        }

        // Retourner l'image en réponse
        $response = new Response($cachedImage->get(), 200, [
            'Content-Type' => 'image/jpeg',  // Type MIME de l'image
        ]);

        return $response;
    }
}
