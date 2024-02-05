<?php

namespace App\Controller;

use App\Entity\Link;
use App\Repository\LinkRepository;
use App\Service\AppConstants;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Svc\LogBundle\Service\EventLog;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/l/')]
class LinkListController extends _BaseController
{
  #[Route('', name: 'link_list_index')]
  public function index(
    LinkRepository $linkRep,
    #[MapQueryParameter] int $page = 1,
    #[MapQueryParameter('q')] ?string $query = null,
  ): Response {
    $this->denyAccessUnlessGranted('ROLE_USER');

    $queryBuilder = $linkRep->qbShowLinksByUser($this->getUser(), $query);

    $links = Pagerfanta::createForCurrentPageWithMaxPerPage(
      new QueryAdapter($queryBuilder),
      $page,
      200
    );
    $haveToPaginate = $links->haveToPaginate();

    return $this->render('link_list/index.html.twig', [
      'links' => $links,
      'haveToPaginate' => $haveToPaginate,
      'q' => $query,
    ]);
  }

  #[Route('c/{id}', name: 'link_list_call')]
  public function callLink(Link $link, EventLog $eventLog): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');
    $this->denyAccessUnlessGranted('view', $link);

    $eventLog->log($link->getId(), AppConstants::LOG_TYPE_LINK_CALLED, ['level' => EventLog::LEVEL_DATA]);

    return $this->redirect($link->getUrl());
  }
}
