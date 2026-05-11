<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
        $em->persist($order);

        foreach($cart->getAdds() as $add) {
            $orderItem = new OrderItem();
            $orderItem->setProduit($add->getProduct());
            $orderItem->setQuantity($add->getQuantity());
            $orderItem->setOrderRef($order);
            $em->persist($orderItem);
        }

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
            throw $this->createAccessDeniedException();
        }

        return $this->render('order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
