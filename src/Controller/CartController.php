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
    public function add(
        int $id, Request $request, ProduitRepository $produitRepository, CartRepository $cartRepository, AddRepository $addRepository, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        $produit = $produitRepository->find($id);

        if(!$produit) {
            throw $this->createNotFoundException('Product not found');
        }

        if($produit->getStock() <= 0) {
            $this->addFlash('error', 'This product is out of stock.');
            return $this->redirectToRoute('app_home');
        }

        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $quantity = (int) $request->query->get('quantity', 1);
        $quantity = max(1, $quantity);
        $quantity = min($quantity, $produit->getStock());

        $cart = $cartRepository->findOneBy(['user' => $user]);
        if(!$cart) {
            $cart = new Cart();
            $cart->setUser($user);
            $em->persist($cart);
        }

        $add = $addRepository->findOneBy(['cart' => $cart, 'product' => $produit]);
        if($add) {
            $newQuantity = min($add->getQuantity() + $quantity, $produit->getStock());
            $add->setQuantity($newQuantity);
        } else {
            $add = new Add();
            $add->setCart($cart);
            $add->setProduct($produit);
            $add->setQuantity($quantity);
            $em->persist($add);
        }

        $em->flush();
        $this->addFlash('cart_added', $produit->getDesignation());

        $referer = $request->headers->get('referer');
        return $this->redirect($referer ?: $this->generateUrl('app_home'));
    }

    #[Route('/remove/{id}', name: 'app_cart_remove')]
    public function remove(Add $add, EntityManagerInterface $em): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        if($add->getCart()?->getUser() !== $this->getUser()) {
            throw $this->createAccessDeniedException('You do not have permission to remove this item.');
        }

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
