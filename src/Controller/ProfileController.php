<?php

namespace App\Controller;

use App\Form\ProfileType;
use App\Service\UserHelper;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: '/profile')]
class ProfileController extends _BaseController
{
  #[Route(path: '/', name: 'profile')]
  public function edit(Request $request, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $profile = $this->getUser();
    $form = $this->createForm(ProfileType::class, $profile);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();
      $this->addFlash('success', 'Profile successful changed.');

      return $this->redirectToRoute('home');
    }

    return $this->render('profile/profile_edit.html.twig', [
      'profile' => $profile,
      'form' => $form,
    ]);
  }

  #[Route(path: '/delete/', name: 'profile_delete')]
  public function delete(UserHelper $userHelper): Response
  {
    $user = $this->getUser();
    try {
      $delAllowed = $userHelper->userDeleteAllowed($user);
    } catch (\Exception $e) {
      $this->addFlash('warning', $e->getMessage());

      return $this->redirectToRoute('profile');
    }

    if (!$delAllowed) {
      $this->addFlash('warning', 'Delete yourself is not allowed.');

      return $this->redirectToRoute('profile');
    }

    if ($userHelper->userDeleteWithAllData($user)) {
      return $this->redirectToRoute('home');
    } else {
      $this->addFlash('warning', 'Cannot delete your user.');

      return $this->redirectToRoute('profile');
    }
  }
}
