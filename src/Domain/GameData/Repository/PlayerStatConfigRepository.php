<?php

namespace App\Domain\GameData\Repository;

use App\Domain\GameData\Entity\PlayerStatConfig;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerStatConfig|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerStatConfig|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerStatConfig[]    findAll()
 * @method PlayerStatConfig[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerStatConfigRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerStatConfig::class);
    }
}
