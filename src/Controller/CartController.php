<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use App\Entity\Cart;
use App\Entity\Add;
use App\Repository\CartRepository;
use App\Repository\AddRepository;
use App\Repository\ProduitRepository;

#[Route('/cart')]
final class CartController extends AbstractController
{
    #[Route('', name: 'app_cart')]
    public function index(CartRepository $cartRepository): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);

        return $this->render('cart/index.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/add/{id}', name: 'app_cart_add')]
    public function add(int $id, Request $request, ProduitRepository $produitRepository, CartRepository $cartRepository, AddRepository $addRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $produit = $produitRepository->find($id);
        $user = $this->getUser();

        $cart = $cartRepository->findOneBy(['user' => $user]);
        if(!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $em->persist($cart);
        }

        $add = $addRepository->findOneBy(['cart' => $cart, 'product' => $produit]);
        if($add) {
            $add->setQuantity($add->getQuantity() + 1);
        } else {
            $add = new Add();
            $add->setCart($cart);
            $add->setProduct($produit);
            $add->setQuantity(1);
            $em->persist($add);
        }

        $em->flush();
        $this->addFlash('cart_added', $produit->getDesignation());

        $referer = $request->headers->get('referer');
        if($referer) {
            return $this->redirect($referer);
        }

        return $this->redirectToRoute('app_home');
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(Add $add, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $em->remove($add);
        $em->flush();

        return $this->redirectToRoute('app_cart');
    }

    #[Route('/empty', name: 'app_cart_clear')]
    public function empty(CartRepository $cartRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $cart = $cartRepository->findOneBy(['user' => $this->getUser()]);
        if($cart) {
            foreach($cart->getAdds() as $add) {
                $em->remove($add);
            }

            $em->flush();
        }

        return $this->redirectToRoute('app_cart');
    }
}
