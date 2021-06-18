<?php

namespace App\Domain\Jann\Agent\Repository;

use App\Domain\Jann\Agent\Entity\TileTracking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TileTracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileTracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileTracking[]    findAll()
 * @method TileTracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileStateTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileTracking::class);
    }

    // /**
    //  * @return TileTracking[] Returns an array of TileTracking objects
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
    public function findOneBySomeField($value): ?TileTracking
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
