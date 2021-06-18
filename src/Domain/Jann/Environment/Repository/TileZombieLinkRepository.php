<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\Jann\Environment\Entity\TileZombieLink;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TileZombieLink|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileZombieLink|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileZombieLink[]    findAll()
 * @method TileZombieLink[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileZombieLinkRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TileZombieLink::class);
    }

    // /**
    //  * @return TileZombieLink[] Returns an array of TileZombieLink objects
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
    public function findOneBySomeField($value): ?TileZombieLink
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
