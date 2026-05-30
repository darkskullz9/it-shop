<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use App\Entity\Order;
use App\Entity\OrderItem;
use App\Repository\OrderRepository;
use App\Repository\CartRepository;

#[Route('/order')]
#[IsGranted('IS_AUTHENTICATED_FULLY')]
final class OrderController extends AbstractController
{
    #[Route('/create', name: 'app_order_create')]
    public function create(CartRepository $cartRepository, EntityManagerInterface $em): Response {

        /** @var \App\Entity\User $user */
        $user = $this->getUser();
        $cart = $cartRepository->findOneBy(['user'=>$user]);

        if(!$cart || $cart->getAdds()->isEmpty()) {
            $this->addFlash('error', 'Your cart is empty.');
            return $this->redirectToRoute('app_cart');
        }

        $order = new Order;
        $order->setUser($user);
        $order->setDateOrder(new \DateTime());
        $order->setStatus('pending');
        $order->setTotal('0.00');

        $em->persist($order);

        $total = '0.00';

        foreach($cart->getAdds() as $add) {
            $product = $add->getProduct();
            $quantity = $add->getQuantity();

            if(!$product) {
                $this->addFlash('error', 'A product in your cart is no longer available.');
                return $this->redirectToRoute('app_cart');
            }

            if($product->getStock() < $quantity) {
                $this->addFlash('error', sprintf('Not enough stock for product "%s".', $product->getDesignation()));
                return $this->redirectToRoute('app_cart');
            }

            $unitPrice = (string) $product->getPrix();

            $orderItem = new OrderItem();
            $orderItem->setProduit($product);
            $orderItem->setQuantity($quantity);
            $orderItem->setUnitPrice($unitPrice);
            $orderItem->setOrderRef($order);

            $lineTotal = bcmul($unitPrice, (string) $quantity, 2);
            $total = bcadd($total, $lineTotal, 2);

            $product->setStock($product->getStock() - $quantity);

            $em->persist($orderItem);
        }

        $order->setTotal($total);
        $em->persist($order);

        foreach($cart->getAdds() as $add) {
            $em->remove($add);
        }

        $em->flush();

        $this->addFlash('success', 'Order successfully placed!');

        return $this->redirectToRoute('app_order_index');
    }

    #[Route('', name: 'app_order_index')]
    public function index(OrderRepository $orderRepository): Response {
        /** @var \App\Entity\User $user */
        $user = $this->getUser();

        $orders = $orderRepository->findBy(
            ['user' => $user],
            ['dateOrder' => 'DESC']
        );

        return $this->render('order/index.html.twig', [
            'orders' => $orders,
        ]);
    }

    #[Route('/{id}', name: 'app_order_show')]
    public function show(Order $order): Response {
        if($order->getUser() !== $this->getUser()) {
            throw new AccessDeniedException();
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
