<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use App\Repository\ProduitRepository;

class PanierService
{
    protected SessionInterface $session;
    protected float $tva = 0.2;

    public function __construct(
        RequestStack $requestStack, // on injecte RequestStack
        protected readonly ProduitRepository $produitRepository
    ) {
        $this->session = $requestStack->getSession(); // on récupère la session depuis RequestStack
    }

    /**
     * retourne le panier (tableau par la session)
     *
     * @return array
     */
    protected function getPanier(): array
    {
        return $this->session->get('panier', []);
    }

    /**
     * sauvegarde le panier avec son contenu (update)
     *
     * @return void
     */
    protected function savePanier($cart)
    {
        $this->session->set('panier', $cart);
        $this->session->set('panierData', $this->getFullPanier());
    }
    
    /**
     * vider le panier
     *
     * @return void
     */
    public function empty()
    {
        $this->savePanier([]);
    }

    /**
     * remove
     *
     * @return void
     */
    public function remove(int $id)
    {
        $cart = $this->getPanier();
        unset($cart[$id]);

        $this->savePanier($cart);
    }

    /**
     * ajout d'un produit dans le panier
     *
     * @return void
     */
    public function ajouterProduit($id)
    {
        $cart = $this->getPanier();

        if(isset($cart[$id])) {
            
            $cart[$id]++;
        }else{
            $cart[$id] = 1;
        }
        

        $this->savePanier($cart);
    }

    /**
     * supprime un produit dans le panier
     *
     * @return void
     */
    public function deleteProduitInCart(int $id) {

        $cart = $this->getPanier();

        if(!isset($cart[$id])) { // si le produit extiste bien dans le panier
            return;
        }

        if($cart[$id] === 1) { //// si le panier à  moins 1 produit de cet id... 

            $this->remove($id);
            return;
        } 

        $cart[$id]--;

        $this->savePanier($cart); // et tu set!
    }

    /**
     * retourne le panier complet
     *
     * @return array
     */
    public function getFullPanier(): array
    {
        $panier = $this->getPanier();

        $fullPanier = [];

        $quantite_cart = 0;

        $subTotal = 0;

        foreach ($panier as $id => $quantite) {
            $produit = $this->produitRepository->find($id);

            if ($produit) {

                $fullPanier['produits'][]=
                [
                    "quantite" => $quantite,
                    "produit" => $produit
                ];

                $quantite_cart += $quantite;
                $subTotal += $quantite * $produit->getPrix()/100;

            }else{
                $this->deleteProduitInCart($id);
            }
            
        }

        $fullPanier['data'] = [
            "quantite_cart" => $quantite_cart,
            "subTotalHT" => $subTotal,
            "Taxe" => round($subTotal * $this->tva,2),
            "subTotalTTC" => round(($subTotal + ($subTotal * $this->tva)), 2)
        ];

        return $fullPanier;
        
    }

}