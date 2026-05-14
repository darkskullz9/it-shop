<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Repository\ProduitRepository;

use App\Entity\Produit;

final class ProduitController extends AbstractController
{
    #[Route('/produit/{id}', name: 'app_produit')]
    public function produit(int $id, ProduitRepository $produitRepository): Response
    {
        $produit = $produitRepository->find($id);

        if(!$produit) {
            $this->addFlash('error', 'This product doesn\'t exist anymore.');
            return $this->redirectToRoute('app_home');
        }

        return $this->render('produit/product.html.twig', [
            'produit' => $produit,
        ]);
    }
}
