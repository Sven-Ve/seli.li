<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkType;
use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use App\Service\CategoryHelper;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manage/link')]
class LinkController extends _BaseController
{
  #[Route('/', name: 'app_link_index', methods: ['GET'])]
  public function index(LinkRepository $linkRep, CategoryRepository $categoryRep, Request $request): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $currentCategory = null;
    $catId = $request->query->get('catId');
    $page = $request->query->get('page', 1);
    if ($catId) {
      $currentCategory = $categoryRep->findOneBy(['id' => $catId, 'user' => $this->getUser()]);
      if ($currentCategory) {
        $queryBuilder = $linkRep->qbLinksByUserAndCategory($this->getUser(), $currentCategory);
      } else {
        $queryBuilder = null;
      }
    } else {
      $queryBuilder = $linkRep->qbAllLinksByUser($this->getUser());
    }
    if ($queryBuilder) {
      $links = new Pagerfanta(new QueryAdapter($queryBuilder));
      $links->setMaxPerPage(5);
      $links->setCurrentPage($page);
      $haveToPaginate = $links->haveToPaginate();
    } else {
      $links = null;
      $haveToPaginate = false;
    }

    $template = $request->query->get('ajax') ? '_list.html.twig' : 'index.html.twig';

    return $this->render('link/' . $template, [
      'links' => $links,
      'categories' => $categoryRep->getCategoryByUser($this->getUser()),
      'currentCategory' => $currentCategory,
      'haveToPaginate' => $haveToPaginate,
    ]);
  }

  #[Route('/new', name: 'app_link_new', methods: ['GET', 'POST'])]
  public function new(Request $request, LinkRepository $linkRepository, CategoryHelper $categoryHelper): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $link = new Link();
    $link->setCategory($categoryHelper->getDefaultCategory($this->getUser()));

    $form = $this->createForm(LinkType::class, $link);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $link->setUser($this->getUser());
      $linkRepository->save($link, true);

      return $this->redirectToRoute('app_link_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('link/new.html.twig', [
      'link' => $link,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'app_link_show', methods: ['GET'])]
  public function show(Link $link): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $this->denyAccessUnlessGranted('view', $link);

    return $this->render('link/show.html.twig', [
      'link' => $link,
    ]);
  }

  #[Route('/{id}/edit', name: 'app_link_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Link $link, LinkRepository $linkRepository): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $this->denyAccessUnlessGranted('edit', $link);
    $form = $this->createForm(LinkType::class, $link);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $linkRepository->save($link, true);

      return $this->redirectToRoute('app_link_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->renderForm('link/edit.html.twig', [
      'link' => $link,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'app_link_delete', methods: ['POST'])]
  public function delete(Request $request, Link $link, LinkRepository $linkRepository): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $this->denyAccessUnlessGranted('edit', $link);
    if ($this->isCsrfTokenValid('delete' . $link->getId(), $request->request->get('_token'))) {
      $linkRepository->remove($link, true);
    }

    return $this->redirectToRoute('app_link_index', [], Response::HTTP_SEE_OTHER);
  }
}
