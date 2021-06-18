<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\Jann\Environment\Entity\PlayerState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerState|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerState|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerState[]    findAll()
 * @method PlayerState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerState::class);
    }

    // /**
    //  * @return PlayerState[] Returns an array of PlayerState objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PlayerState
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
