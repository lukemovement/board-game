<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\Jann\Environment\Entity\RouteTileLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RouteTileLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method RouteTileLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method RouteTileLink[]    findAll()
 * @method RouteTileLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RouteTileLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RouteTileLink::class);
    }

    // /**
    //  * @return RouteTileLink[] Returns an array of RouteTileLink objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RouteTileLink
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
