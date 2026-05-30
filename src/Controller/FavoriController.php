<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Produit;

final class FavoriController extends AbstractController
{
    #[Route('/private-favori/{id}', name: 'app_favori')]
    public function toggle(Produit $produit, EntityManagerInterface $em, Request $request): Response {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $referer = $request->headers->get('referer');
        $u = $this->getUser();

        if (!$u instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException();
        }

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
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $u = $this->getUser();

        if (!$u instanceof \App\Entity\User) {
            throw $this->createAccessDeniedException();
        }
        
        $favoris = $u->getProduits();

        return $this->render('favori/liste-favoris.html.twig', [
            'favoris' => $favoris,
        ]);
    }
}
