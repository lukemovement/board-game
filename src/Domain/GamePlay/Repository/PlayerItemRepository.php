<?php

namespace App\Domain\GamePlay\Repository;

use App\Domain\GamePlay\Entity\PlayerItem;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerItem|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerItem|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerItem[]    findAll()
 * @method PlayerItem[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerItemRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerItem::class);
    }

    // /**
    //  * @return PlayerItem[] Returns an array of PlayerItem objects
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
    public function findOneBySomeField($value): ?PlayerItem
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
