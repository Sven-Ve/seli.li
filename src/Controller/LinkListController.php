<?php

namespace App\Controller;

use App\Entity\Link;
use App\Repository\LinkRepository;
use App\Service\AppConstants;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Svc\LogBundle\Service\EventLog;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/l/')]
class LinkListController extends _BaseController
{
  #[Route('', name: 'link_list_index')]
  public function index(LinkRepository $linkRep, Request $request): Response
  {
    $this->denyAccessUnlessGranted('ROLE_USER');

    $page = $request->query->get('page', 1);
    $query = $request->query->get('q');

    $queryBuilder = $linkRep->qbShowLinksByUser($this->getUser(), $query);
//    dump($queryBuilder->getDQL());
//    dump($queryBuilder->getQuery()->execute());

    $links = new Pagerfanta(new QueryAdapter($queryBuilder));
    $links->setMaxPerPage(200);
    $links->setCurrentPage($page);
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
