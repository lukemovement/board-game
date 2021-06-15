<?php

namespace App\Domain\GameData\Repository;

use App\Domain\GameData\Entity\PlayerStatModifier;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerStatModifier|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerStatModifier|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerStatModifier[]    findAll()
 * @method PlayerStatModifier[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerStatModifierRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerStatModifier::class);
    }

    // /**
    //  * @return PlayerStatModifier[] Returns an array of PlayerStatModifier objects
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
    public function findOneBySomeField($value): ?PlayerStatModifier
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
