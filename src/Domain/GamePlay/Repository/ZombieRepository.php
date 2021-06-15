<?php

namespace App\Domain\GamePlay\Repository;

use App\Domain\GamePlay\Entity\Zombie;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Zombie|null find($id, $lockMode = null, $lockVersion = null)
 * @method Zombie|null findOneBy(array $criteria, array $orderBy = null)
 * @method Zombie[]    findAll()
 * @method Zombie[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZombieRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Zombie::class);
    }

    // /**
    //  * @return Zombie[] Returns an array of Zombie objects
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
    public function findOneBySomeField($value): ?Zombie
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
