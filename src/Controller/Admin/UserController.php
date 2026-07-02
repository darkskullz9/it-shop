<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\AdminUserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/users')]
#[IsGranted('ROLE_ADMIN')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_admin_users')]
    public function index(UserRepository $userRepository): Response
    {
        $users = $userRepository->findBy([], [
            'email' => 'ASC',
        ]);

        return $this->render('admin/user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', requirements: ['id' => '\d+'])]
    public function edit(User $user, Request $request, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(AdminUserType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $roles = $form->get('roles')->getData();

            // Sécurité : on garde toujours ROLE_USER.
            $roles[] = 'ROLE_USER';
            $roles = array_values(array_unique($roles));

            // Sécurité : éviter qu'un admin se retire son propre rôle admin.
            $currentUser = $this->getUser();

            if (
                $currentUser instanceof User
                && $currentUser->getId() === $user->getId()
                && !in_array('ROLE_ADMIN', $roles, true)
            ) {
                $this->addFlash('error', 'Tu ne peux pas retirer ton propre rôle administrateur.');

                return $this->redirectToRoute('app_admin_user_edit', [
                    'id' => $user->getId(),
                ]);
            }

            $user->setRoles($roles);

            $em->flush();

            $this->addFlash('success', 'Les rôles de l’utilisateur ont été modifiés.');

            return $this->redirectToRoute('app_admin_users');
        }

        return $this->render('admin/user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}