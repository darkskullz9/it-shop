<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Entity\Produit;
use App\Entity\Category;
use App\Entity\Order;
use App\Form\ProduitType;
use App\Form\CategoryType;
use App\Repository\ProduitRepository;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;

#[Route('/admin')]
#[IsGranted('ROLE_ADMIN')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'app_admin')]
    public function index(): Response {
        return $this->render('admin/index.html.twig');
    }

    #[Route('/produits', name: 'app_admin_produits')]
    public function produits(ProduitRepository $produitRepository): Response {
        return $this->render('admin/produit/index.html.twig', [
            'produits' => $produitRepository->findAll(),
        ]);
    }

    #[Route('/produits/new', name: 'app_admin_produit_new')]
    public function produitNew(Request $request, EntityManagerInterface $em): Response {
        $produit = new Produit();
        $form = $this->createForm(ProduitType::class, $produit, [
            'validation_groups' => ['Default', 'creation'],
        ]);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/',
                    $newFilename
                );

                $produit->setImage($newFilename);
            }

            $em->persist($produit);
            $em->flush();
            $this->addFlash('success', 'Product created.');
            return $this->redirectToRoute('app_admin_produits');
        }

        return $this->render('admin/produit/form.html.twig', [
            'form' => $form,
            'title' => 'New product'
        ]);
    }

    #[Route('/produits/{id}/edit', name: 'app_admin_produit_edit')]
    public function produitEdit(Produit $produit, Request $request, EntityManagerInterface $em): Response
    {
        $oldImage = $produit->getImage();

        $form = $this->createForm(ProduitType::class, $produit, [
            'validation_groups' => ['Default'],
        ]);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $imageFile = $form->get('imageFile')->getData();

            if($imageFile) {
                $newFilename = uniqid() . '.' . $imageFile->guessExtension();

                $imageFile->move(
                    $this->getParameter('kernel.project_dir') . '/public/uploads/',
                    $newFilename
                );

                if($oldImage) {
                    $oldImagePath = $this->getParameter('kernel.project_dir') . '/public/uploads/' . $oldImage;
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }

                $produit->setImage($newFilename);
            } else {
                $produit->setImage($oldImage);
            }

            $em->flush();
            $this->addFlash('success', 'Product updated.');
            return $this->redirectToRoute('app_admin_produits');
        }

        return $this->render('admin/produit/form.html.twig', [
            'form' => $form,
            'title' => 'Edit product',
            'produit' => $produit,
        ]);
    }

    #[Route('/produits/{id}/delete', name: 'app_admin_produit_delete', methods: ['POST'])]
    public function produitDelete(Produit $produit, EntityManagerInterface $em): Response {
        $em->remove($produit);
        $em->flush();
        $this->addFlash('success', 'Product deleted.');
        return $this->redirectToRoute('app_admin_produits');
    }

    #[Route('/categories', name: 'app_admin_categories')]
    public function categories(CategoryRepository $categoryRepository): Response {
        return $this->render('admin/category/index.html.twig', [
            'categories' => $categoryRepository->findAll(),
        ]);
    }

    #[Route('/categories/new', name: 'app_admin_category_new')]
    public function categoryNew(Request $request, EntityManagerInterface $em): Response {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Category created.');
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form,
            'title' => 'New category',
        ]);
    }

    #[Route('/categories/{id}/edit', name: 'app_admin_category_edit')]
    public function categoryEdit(Category $category, Request $request, EntityManagerInterface $em): Response {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Category updated.');
            return $this->redirectToRoute('app_admin_categories');
        }

        return $this->render('admin/category/form.html.twig', [
            'form' => $form,
            'title' => 'Edit category',
        ]);
    }

    #[Route('/categories/{id}/delete', name: 'app_admin_category_delete', methods: ['POST'])]
    public function categoryDelete(Category $category, EntityManagerInterface $em): Response {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'Category deleted.');
        return $this->redirectToRoute('app_admin_categories');
    }

    #[Route('/orders', name: 'app_admin_orders')]
    public function orders(OrderRepository $orderRepository): Response {
        return $this->render('admin/order/index.html.twig', [
            'orders' => $orderRepository->findBy([], ['dateOrder' => 'DESC']),
        ]);
    }

    #[Route('/orders/{id}', name: 'app_admin_order_show')]
    public function orderShow(Order $order, Request $request, EntityManagerInterface $em): Response {
        if ($request->isMethod('POST')) {
            $order->setStatus($request->request->get('status'));
            $em->flush();
            $this->addFlash('success', 'Status updated.');
            return $this->redirectToRoute('app_admin_orders');
        }

        return $this->render('admin/order/show.html.twig', [
            'order' => $order,
        ]);
    }
}
