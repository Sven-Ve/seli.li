<?php

namespace App\EventListener;

use App\Entity\_DefaultSuperclass;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\Persistence\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Security;

/**
 * set username and date for create and update entities, based on DefaultSuperclass.
 *
 * service has du defined in /config/services.yaml like this:
 *     App\EventListener\EntityBlameable:
 *       tags:
 *       - { name: doctrine.orm.entity_listener, event: prePersist, entity: 'App\Entity\_DefaultSuperclass' }
 *       - { name: doctrine.orm.entity_listener, event: preUpdate, entity: 'App\Entity\_DefaultSuperclass' }
 */
class EntityBlameable
{
  public function __construct(private readonly Security $security)
  {
  }

  #[ORM\PreUpdate]
    public function preUpdate(_DefaultSuperclass $entity, LifecycleEventArgs $event): void
    {
      $user = $this->security->getUser();
      if ($user) {
        $entity->setUpdatedBy($user->getId() ?? -1);
      } else {
        $entity->setUpdatedBy(-1);
      }
      $entity->setUpdatedAt(new \DateTime());
    }

  #[ORM\PrePersist]
    public function prePersist(_DefaultSuperclass $entity, LifecycleEventArgs $event): void
    {
      $user = $this->security->getUser();
      if ($user) {
        $entity->setCreatedBy($user->getId() ?? -1);
      } else {
        $entity->setCreatedBy(-1);
      }
      $entity->setCreatedAt(new \DateTime());
    }
}
