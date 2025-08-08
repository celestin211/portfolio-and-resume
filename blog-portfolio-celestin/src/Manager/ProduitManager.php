<?php

namespace App\Manager;

use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;

class ProduitManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    public function listeProduit()
    {
        $produits = $this->em->getRepository(Produit::class)->findAll();

        $listeProduits = [];
        foreach ($produits as $produit) {
            $listeProduits[] = [
                mb_strtoupper($produit->getNom()),
                mb_strtoupper($produit->getPrix()),
                mb_strtoupper($produit->getQuantite()),
                mb_strtoupper($produit->getDescription()),
            ];
        }

        return $listeProduits;
    }

    public function create(Produit $produit)
    {
        // On vérifie si le produit existe déjà en base
        $produitBase = $this->em->getRepository(Produit::class)->findOneBy([
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrix(),
            'quantite' => $produit->getQuantite(),
            'imageFilename' => $produit->getImageFilename(),
            'supprime' => false,  // Vérification si le produit n'est pas supprimé
        ]);

        // Si le produit existe déjà, on retourne null
        if ($produitBase) {
            return null; // Produit déjà existant, on ne le recrée pas
        } else {
            // Sinon, on crée un nouveau produit
            $createdAt = new \DateTime();
            $produit->setCreatedAt($createdAt);
            $produit->setSupprime(false); // Le produit est initialement non supprimé
            $this->em->persist($produit); // On persiste le produit
            $this->em->flush(); // Sauvegarde en base de données

            return $produit; // Retourner l'objet produit créé
        }
    }

    public function update(Produit $produit)
    {
        // On vérifie si le produit existe déjà en base
        $produitBase = $this->em->getRepository(Produit::class)->findOneBy([
            'nom' => $produit->getNom(),
            'prix' => $produit->getPrix(),
            'quantite' => $produit->getQuantite(),
            'supprime' => 0,
        ]);

        if ($produitBase && $produitBase->getId() != $produit->getId()) {
            return null;
        } else {
            $produit->setUpdatedAt(new \DateTime());
            $produit->setSupprime(false);
            $this->em->persist($produit);
            $this->em->flush();

            return $produit;
        }
    }

    public function delete(Produit $produit)
    {
        $this->em->remove($produit);
        $this->em->flush();
    }
}
