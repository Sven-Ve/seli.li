<?php

namespace App\Repository;

use App\Entity\Category;
use App\Entity\Link;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\Query\QueryException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;
use function Symfony\Component\String\u;

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

  /**
   * @throws QueryException
   */
  public function qbAllLinksByUser(User $user): QueryBuilder
  {
    return $this->createQueryBuilder('l')
      ->addCriteria(self::createUserCriteria($user));
  }

  /**
   * @throws QueryException
   */
  public function qbLinksByUserAndCategory(User $user, Category $category): QueryBuilder
  {
    return $this->createQueryBuilder('l')
      ->addCriteria(self::createUserCriteria($user))
      ->addCriteria(self::createCategoryCriteria($category));
  }

  /**
   * @throws QueryException
   */
  public function qbShowLinksByUser(?User $user = null): QueryBuilder
  {
    $queryBuilder = $this->createQueryBuilder('l');
    if ($user !== null) {
      $queryBuilder->addCriteria(self::createUserCriteria($user));
    }

    return $queryBuilder
      ->addSelect('c')
      ->leftJoin('l.category', 'c')
      ->addOrderBy('c.name', 'asc')
      ->addOrderBy('l.name', 'asc');
  }

  /**
   * @throws QueryException
   */
  public function qbFindBySearchQuery(User $user, string $query): QueryBuilder
  {
    $queryBuilder = $this->qbShowLinksByUser();

    $searchTerms = self::extractSearchTerms($query);
    if (\count($searchTerms) > 0) {
      foreach ($searchTerms as $key => $term) {
        $queryBuilder
          ->orWhere('l.name LIKE :v_' . $key)
          ->orWhere('l.description LIKE :v_' . $key)
          ->setParameter('v_' . $key, '%' . $term . '%');
      }
    }
    $queryBuilder
      ->addCriteria(self::createUserCriteria($user)); // add user query after search terms because "or" and brackets

    return $queryBuilder;
  }

  private static function extractSearchTerms(string $searchQuery): array
  {
    $searchQuery = u($searchQuery)->replaceMatches('/[[:space:]]+/', ' ')->trim();
    $terms = array_unique($searchQuery->split(' '));

    // ignore the search terms that are too short
    return array_filter($terms, static function ($term) {
      return 2 <= $term->length();
    });
  }
}
