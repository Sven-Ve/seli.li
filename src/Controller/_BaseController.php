<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

/**
 * @method User getUser()
 */
abstract class _BaseController extends AbstractController
{
  protected function getAppName(): string
  {
    return $this->getParameter('app.name');
  }
}
