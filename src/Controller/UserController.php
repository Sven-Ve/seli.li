<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Service\UserHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/user')]
class UserController extends _BaseController
{
  /**
   * show all user.
   */
  #[Route(path: '/', name: 'user_index', methods: ['GET'])]
  public function index(UserRepository $userRepository): Response
  {
    return $this->render('user/index.html.twig', [
      'users' => $userRepository->findAll(),
    ]);
  }

  /**
   * edit user.
   */
  #[Route(path: '/{id}/edit', name: 'user_edit', methods: ['GET', 'POST'])]
  public function edit(User $user, Request $request, EntityManagerInterface $entityManager): Response
  {
    $form = $this->createForm(UserType::class, $user);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      if (($user->getId() == $this->getUser()->getId()) and $user->isBlocked()) {
        $user->setIsBlocked(false);
        $this->addFlash('warning', 'You cannot block yourself.');
      }
      $entityManager->flush();

      return $this->redirectToRoute('user_index');
    }

    return $this->renderForm('user/edit.html.twig', [
      'user' => $user,
      'form' => $form,
    ]);
  }

  /**
   * delete user.
   */
  #[Route(path: '/{id}/delete', name: 'user_delete', methods: ['POST'])]
  public function delete(Request $request, User $user, EntityManagerInterface $entityManager, UserHelper $userHelper): Response
  {
    if ($user->getId() == $this->getUser()->getId()) {
      $this->addFlash('danger', "You can't delete yourself!");

      return $this->redirectToRoute('user_index');
    }

    if (!$userHelper->userDeleteAllowed($user, true)) {
      $this->addFlash('danger', 'Delete not allowed!');

      return $this->redirectToRoute('user_index');
    }

    if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->request->get('_token'))) {
      $userHelper->userDeleteWithAllData($user);
    }

    return $this->redirectToRoute('user_index');
  }
}
