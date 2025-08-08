<?php


namespace App\Manager;

use App\Entity\Categorie;
use App\Entity\Produit;
use Doctrine\ORM\EntityManagerInterface;

class CategorieManager
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    )
    {
    }

    public function listeCategorie()
    {
        // Récupérer toutes les catégories
        $categories = $this->em->getRepository(Categorie::class)->findBy(['actif' => true]); // Optionnel : si vous voulez seulement les catégories actives

        $listeCategorie = [];
        foreach ($categories as $categorie) {
            // Créer un tableau associatif avec les clés explicites pour sérialisation
            $listeCategorie[] = [
                'nom' => $categorie->getNom(), // Nom de la catégorie
                'description' => $categorie->getDescription(), // Description
                'image' => $categorie->getImage(), // Image de la catégorie
                'actif' => $categorie->isActif(), // Statut actif
            ];
        }

        return $listeCategorie;
    }

    public function create(Categorie $categorie)
    {
        // On vérifie si le produit existe déjà en base
        $produitBase = $this->em->getRepository(Categorie::class)->findBy([
            'nom' => $categorie->getNom(),
            'description' => $categorie->getDescription(),
            'image' => $categorie->getImage(),
            'slug' => $categorie->getSlug(),
            'is_actif' => $categorie->isActif(),
            'metaTitle' => $categorie->getMetaTitle(),
            'metaDescription' => $categorie->getMetaDescription(),
        ]);

        if ($produitBase) {
            return null;
        } else {
            $this->em->persist($categorie);
            $this->em->flush();

            return $categorie;
        }
    }

    public function update(Categorie $produit)
    {
        // On vérifie si le produit existe déjà en base
        $categorieBase = $this->em->getRepository(Categorie::class)->findOneBy([
            'nom' => $produit->getNom(),
            'description' => $produit->getDescription(),
            'image' => $produit->getImage(),
            'slug' => $produit->getSlug(),
            'is_actif' => $produit->isActif(),
            'metaTitle' => $produit->getMetaTitle(),
            'metaDescription' => $produit->getMetaDescription(),
        ]);

        if ($categorieBase && $categorieBase->getId() != $produit->getId()) {
            return null;
        } else {
            $produit->addProduit($categorieBase);
            $this->em->persist($produit);
            $this->em->flush();

            return $produit;
        }
    }

    public function delete(Categorie $categorie)
    {
        $this->em->remove($categorie);
        $this->em->flush();
    }
}
