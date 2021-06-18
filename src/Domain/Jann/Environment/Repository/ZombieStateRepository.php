<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\Jann\Environment\Entity\ZombieState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ZombieState|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZombieState|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZombieState[]    findAll()
 * @method ZombieState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZombieStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZombieState::class);
    }

    // /**
    //  * @return ZombieState[] Returns an array of ZombieState objects
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
    public function findOneBySomeField($value): ?ZombieState
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
