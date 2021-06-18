<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\Jann\Environment\Entity\TileRouteLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TileRouteLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileRouteLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileRouteLink[]    findAll()
 * @method TileRouteLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileRouteLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileRouteLink::class);
    }

    // /**
    //  * @return TileRouteLink[] Returns an array of TileRouteLink objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TileRouteLink
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
