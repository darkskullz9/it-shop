<?php

namespace App\Controller;

use App\Entity\Produit;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class FavoriController extends AbstractController
{
    #[Route('/private-favori/{id}', name: 'app_favori')]
    public function toggle(Produit $produit, EntityManagerInterface $em, Request $request): Response {
        $referer = $request->headers->get('referer');
        $u = $this->getUser();

        if($u->getProduits()->contains($produit)) {
            $u->removeProduit($produit);
        } else {
            $u->addProduit($produit);
        }

        $em->persist($u);
        $em->flush();

        return $this->redirect($referer ?? $this->generateUrl('app_home'));
    }

    #[Route('/private-liste-favoris', name: 'app_liste_favoris')]
    public function listeFavoris(): Response {
        $favoris = $this->getUser()->getProduits();

        return $this->render('favori/liste-favoris.html.twig', [
            'favoris' => $favoris,
        ]);
    }
}
