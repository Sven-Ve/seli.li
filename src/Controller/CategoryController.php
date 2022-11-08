<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\CategoryHelper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/manage/category')]
class CategoryController extends _BaseController
{
  #[Route(path: '/', name: 'category_index', methods: ['GET'])]
  public function index(CategoryRepository $categoryRepository, Request $request, CategoryHelper $categoryHelper): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $categoryHelper->getDefaultCategory($this->getUser());
    $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';

    return $this->render('category/' . $template, [
      'categories' => $categoryRepository->getCategoryByUser($this->getUser()),
    ]);
  }

  /**
   * create a category.
   */
  #[Route(path: '/new', name: 'category_new', methods: ['GET', 'POST'])]
  public function new(Request $request, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $category = new Category($this->getUser());
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->persist($category);
      $entityManager->flush();

      return $this->redirectToRoute('category_index');
    }

    return $this->renderForm('category/new.html.twig', [
      'category' => $category,
      'form' => $form,
    ]);
  }

  /**
   * edit a category.
   */
  #[Route(path: '/{id}/edit', name: 'category_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Category $category, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $this->denyAccessUnlessGranted('edit', $category);
    $form = $this->createForm(CategoryType::class, $category);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $entityManager->flush();

      return $this->redirectToRoute('category_index');
    }

    return $this->renderForm('category/edit.html.twig', [
      'category' => $category,
      'form' => $form,
    ]);
  }

  /**
   * Delete a category.
   */
  #[Route(path: '/{id}', name: 'category_delete', methods: ['POST'])]
  public function delete(Request $request, Category $category, CategoryHelper $categoryHelper, EntityManagerInterface $entityManager): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $this->denyAccessUnlessGranted('edit', $category);
    if ($this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
      $this->denyAccessUnlessGranted('edit', $category);

      $categoryHelper->resetToDefaultCategory($category, $this->getUser());
      try {
        $entityManager->remove($category);
        $entityManager->flush();
      } catch (Exception) {
        $this->addFlash('danger', 'Cannot delete this category. Maybe some redirects use it?');
      }
    }

    return $this->redirectToRoute('category_index');
  }

  /**
   * set the default category per ajax call.
   */
  #[Route(path: '/setDefault/{id}', name: 'category_set_default', methods: ['GET', 'POST'])]
  public function setDefault(Category $category, CategoryHelper $categoryHelper): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    if ($categoryHelper->setDefault($category, $this->getUser())) {
      return new Response(null, Response::HTTP_NO_CONTENT);
    } else {
      return new Response(null, Response::HTTP_UNPROCESSABLE_ENTITY);
    }
  }
}
