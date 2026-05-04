<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    public function search(): Response {
        return $this->render('base/search.html.twig', []);
    }

    #[Route('/results', name: 'app_results')]
    public function results(Request $request, ProduitRepository $produitRepository): Response {
        $word=null;
        $produits = [];

        if($request->isMethod('GET')){
            if($request->get("q")){
                $word=$request->get("q");

                $produits=$produitRepository->search($word);
            }
        }
        return $this->render('base/results.html.twig', ["produits"=>$produits, "word"=>$word]);
    }
}