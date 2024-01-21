<?php

namespace App\Controller;

use App\Entity\Link;
use App\Form\LinkType;
use App\Repository\CategoryRepository;
use App\Repository\LinkRepository;
use App\Service\AppConstants;
use App\Service\CategoryHelper;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Svc\LogBundle\Service\EventLog;
use Svc\ParamBundle\Repository\ParamsRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/manage/link')]
class LinkController extends _BaseController
{
  #[Route('/', name: 'app_link_index', methods: ['GET'])]
  public function index(
    LinkRepository $linkRep,
    CategoryRepository $categoryRep,
    ParamsRepository $paramsRep,
    #[MapQueryParameter] int $page = 1,
    #[MapQueryParameter] int $catId = null,
    #[MapQueryParameter] string $sort = 'name',
    #[MapQueryParameter] string $sortDirection = 'asc',
    #[MapQueryParameter('q')] string $query = null,
  ): Response {
    $this->denyAccessUnlessGranted('ROLE_USER');

    $linksPerPage = $paramsRep->getInteger(AppConstants::PARAM_LINKS_PER_PAGE);
    if (null === $linksPerPage) {
      $linksPerPage = 20;
      $paramsRep->setInteger(AppConstants::PARAM_LINKS_PER_PAGE, $linksPerPage, 'max. Links per Page');
    }

    $validSorts = ['name', 'category'];
    $sort = in_array($sort, $validSorts) ? $sort : 'name';
    $sortDirection = in_array($sortDirection, ['asc', 'desc']) ? $sortDirection : 'asc';

    $currentCategory = null;
    if ($catId) {
      $currentCategory = $categoryRep->findOneBy(['id' => $catId, 'user' => $this->getUser()]);
      if ($currentCategory) {
        $queryBuilder = $linkRep->qbShowLinksByUser($this->getUser(), $query, $currentCategory, $sort, $sortDirection);
      } else {
        $queryBuilder = null;
      }
    } else {
      $queryBuilder = $linkRep->qbShowLinksByUser($this->getUser(), $query, null, $sort, $sortDirection);
    }
    if ($queryBuilder) {
      $links = Pagerfanta::createForCurrentPageWithMaxPerPage(
        new QueryAdapter($queryBuilder),
        $page,
        $linksPerPage
      );
      $haveToPaginate = $links->haveToPaginate();
    } else {
      $links = null;
      $haveToPaginate = false;
    }

    return $this->render('link/index.html.twig', [
      'links' => $links,
      'categories' => $categoryRep->getCategoryByUser($this->getUser()),
      'currentCategory' => $currentCategory,
      'haveToPaginate' => $haveToPaginate,
      'sort' => $sort,
      'sortDirection' => $sortDirection,
      'q' => $query,
    ]);
  }

  #[Route('/new', name: 'app_link_new', methods: ['GET', 'POST'])]
  public function new(Request $request, LinkRepository $linkRepository, CategoryHelper $categoryHelper, EventLog $eventLog): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $link = new Link();
    $link->setCategory($categoryHelper->getDefaultCategory($this->getUser()));
    $link->setUser($this->getUser());

    $form = $this->createForm(LinkType::class, $link);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $linkRepository->save($link, true);
      $eventLog->log($link->getId(), AppConstants::LOG_TYPE_LINK_CREATED, ['level' => EventLog::LEVEL_INFO]);
      $this->addFlash('success', 'Link added.');

      return $this->redirectToRoute('app_link_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('link/new.html.twig', [
      'link' => $link,
      'form' => $form,
    ]);
  }

  #[Route('/{id}/edit', name: 'app_link_edit', methods: ['GET', 'POST'])]
  public function edit(Request $request, Link $link, LinkRepository $linkRepository, EventLog $eventLog): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $this->denyAccessUnlessGranted('edit', $link);
    $form = $this->createForm(LinkType::class, $link);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
      $linkRepository->save($link, true);
      $eventLog->log($link->getId(), AppConstants::LOG_TYPE_LINK_CHANGED, ['level' => EventLog::LEVEL_INFO]);
      $this->addFlash('success', 'Link saved.');

      return $this->redirectToRoute('app_link_index', [], Response::HTTP_SEE_OTHER);
    }

    return $this->render('link/edit.html.twig', [
      'link' => $link,
      'form' => $form,
    ]);
  }

  #[Route('/{id}', name: 'app_link_delete', methods: ['POST'])]
  public function delete(Request $request, Link $link, LinkRepository $linkRepository, EventLog $eventLog): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $this->denyAccessUnlessGranted('edit', $link);
    if ($this->isCsrfTokenValid('delete' . $link->getId(), $request->request->get('_token'))) {
      $eventLog->log($link->getId(), AppConstants::LOG_TYPE_LINK_DELETED, ['level' => EventLog::LEVEL_WARN, 'message' => 'Link deleted: ' . $link->getName()]);
      $linkRepository->remove($link, true);
      $this->addFlash('success', 'Link deleted.');
    }

    return $this->redirectToRoute('app_link_index', [], Response::HTTP_SEE_OTHER);
  }
}
