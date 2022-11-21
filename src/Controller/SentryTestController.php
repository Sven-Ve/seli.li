<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Component\Routing\Annotation\Route;

class SentryTestController extends _BaseController
{

  public function __construct(private readonly LoggerInterface $logger)
  {
  }

  /**
   * @Route(name="sentry_test", path="/_sentry-test")
   */
  public function testLog()
  {
    // the following code will test if monolog integration logs to sentry
    $this->logger->error('My custom logged error.');

    // the following code will test if an uncaught exception logs to sentry
    throw new \RuntimeException('Example exception.');
  }
}
