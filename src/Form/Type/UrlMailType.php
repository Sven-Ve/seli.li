<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Form\Type;

use App\Form\EventListener\FixUrlMailProtocolListener;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;

class UrlMailType extends UrlType
{
  public function buildForm(FormBuilderInterface $builder, array $options): void
  {
    if (null !== $options['default_protocol']) {
      $builder->addEventSubscriber(new FixUrlMailProtocolListener($options['default_protocol']));
    }
  }
}
