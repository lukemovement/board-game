<?php

namespace App\Domain\GamePlay\Repository;

use App\Domain\GamePlay\Entity\SearchableInteraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method SearchableInteraction|null find($id, $lockMode = null, $lockVersion = null)
 * @method SearchableInteraction|null findOneBy(array $criteria, array $orderBy = null)
 * @method SearchableInteraction[]    findAll()
 * @method SearchableInteraction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class SearchableInteractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, SearchableInteraction::class);
    }

    // /**
    //  * @return SearchableInteraction[] Returns an array of SearchableInteraction objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('s.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?SearchableInteraction
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
