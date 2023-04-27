<?php

namespace App\Repository;

use App\Entity\Authors;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Authors>
 *
 * @method Authors|null find($id, $lockMode = null, $lockVersion = null)
 * @method Authors|null findOneBy(array $criteria, array $orderBy = null)
 * @method Authors[]    findAll()
 * @method Authors[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Authors::class);
    }

    public  function findByFullName(string $search, bool $archived = false): array
    {/*if search == null search ''*/
        if ($search == null) {
            $search = '';
        }

        $queryBuilder = $this->createQueryBuilder('a')
            ->andWhere('a.archived = :archived')
            ->setParameter('archived', $archived)
            ->andWhere('a.FullName LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->orderBy('a.FullName', 'ASC')
        ;

        return $queryBuilder->getQuery()->getResult();
    }
    public function save(Authors $entity, bool $flush = true): void
    {
        $entity->setArchived(false);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Authors $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
    public function removeAuthor(Authors $entity, bool $flush = false): void
    {
        $entity->setArchived(true);
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }
//    /**
//     * @return Authors[] Returns an array of Authors objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('a.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Authors
//    {
//        return $this->createQueryBuilder('a')
//            ->andWhere('a.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
