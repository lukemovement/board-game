<?php

namespace App\Domain\GamePlay\Repository;

use App\Domain\GamePlay\Entity\PlayerSlots;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerSlots|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerSlots|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerSlots[]    findAll()
 * @method PlayerSlots[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerSlotsRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerSlots::class);
    }

    // /**
    //  * @return PlayerSlots[] Returns an array of PlayerSlots objects
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
    public function findOneBySomeField($value): ?PlayerSlots
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
