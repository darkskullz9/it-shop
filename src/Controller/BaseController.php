<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\ProduitRepository;

final class BaseController extends AbstractController {
    #[Route('/', name: 'app_home')]
    public function index(ProduitRepository $produitRepository): Response {
        $produits = $produitRepository -> findAll();

        return $this->render('base/index.html.twig', [
            'produits' => $produits
        ]);
    }
}
