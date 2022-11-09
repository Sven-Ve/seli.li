<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Link;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Link>
 *
 * @method Link|null find($id, $lockMode = null, $lockVersion = null)
 * @method Link|null findOneBy(array $criteria, array $orderBy = null)
 * @method Link[]    findAll()
 * @method Link[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LinkRepository extends ServiceEntityRepository
{
  public function __construct(ManagerRegistry $registry)
  {
    parent::__construct($registry, Link::class);
  }

  public function save(Link $entity, bool $flush = false): void
  {
    $this->getEntityManager()->persist($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  public function remove(Link $entity, bool $flush = false): void
  {
    $this->getEntityManager()->remove($entity);

    if ($flush) {
      $this->getEntityManager()->flush();
    }
  }

  private static function createUserCriteria(User $user): Criteria
  {
    return Criteria::create()
      ->andWhere(Criteria::expr()->eq('user', $user));
  }

  private static function createCategoryCriteria(Category $category): Criteria
  {
    return Criteria::create()
      ->andWhere(Criteria::expr()->eq('category', $category));
  }

  public function qbAllLinksByUser(User $user): QueryBuilder
  {
    return $this->createQueryBuilder('l')
      ->addCriteria(self::createUserCriteria($user));
  }

  public function qbLinksByUserAndCategory(User $user, Category $category): QueryBuilder
  {
    return $this->createQueryBuilder('l')
      ->addCriteria(self::createUserCriteria($user))
      ->addCriteria(self::createCategoryCriteria($category));
  }

  public function qbShowLinksByUser(User $user): QueryBuilder
  {
    return $this->createQueryBuilder('l')
      ->addCriteria(self::createUserCriteria($user))
      ->addSelect('c')
      ->leftJoin('l.category', 'c')
      ->addOrderBy('c.name', 'asc')
      ->addOrderBy('l.name', 'asc')
    ;
  }
}
