<?php

namespace App\Controller;

use App\Repository\LinkRepository;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
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

    $queryBuilder = $linkRep->qbShowLinksByUser($this->getUser());
    dump($queryBuilder->getDQL());
    dump($queryBuilder->getQuery()->execute());
    $links = new Pagerfanta(new QueryAdapter($queryBuilder));
    $links->setMaxPerPage(20);
    $links->setCurrentPage($page);
    $haveToPaginate = $links->haveToPaginate();

    return $this->render('link_list/index.html.twig', [
      'links' => $links,
      'haveToPaginate' => $haveToPaginate,
    ]);
  }
}
