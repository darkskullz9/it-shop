<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Produit;

final class ProduitController extends AbstractController
{
    #[Route('/produit/{id}', name: 'app_produit')]
    public function produit(Produit $produit): Response
    {
        return $this->render('produit/product.html.twig', [
            'produit' => $produit,
        ]);
    }
}
