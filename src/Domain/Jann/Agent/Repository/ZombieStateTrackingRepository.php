<?php

namespace App\Domain\Jann\Agent\Repository;

use App\Domain\Jann\Agent\Entity\ZombieTracking;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ZombieTracking|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZombieTracking|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZombieTracking[]    findAll()
 * @method ZombieTracking[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZombieStateTrackingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZombieTracking::class);
    }

    // /**
    //  * @return ZombieTracking[] Returns an array of ZombieTracking objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('z.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?ZombieTracking
    {
        return $this->createQueryBuilder('z')
            ->andWhere('z.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
