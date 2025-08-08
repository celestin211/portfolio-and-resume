<?php


// src/Event/StockUpdatedEvent.php

namespace App\EventListener;

use App\Entity\Produit;
use Symfony\Contracts\EventDispatcher\Event;

class StockUpdatedEvent extends Event
{
    public const NAME = 'product.stock_updated';  // Nom de l'événement

    private Produit $produit;

    public function __construct(Produit $produit)
    {
        $this->produit = $produit;
    }

    public function getProduit(): Produit
    {
        return $this->produit;
    }
}
