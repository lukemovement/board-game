<?php

namespace App\Domain\GameData\Repository;

use App\Domain\GameData\Entity\ZombieType;
use App\Domain\GamePlay\Entity\Game;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method ZombieType|null find($id, $lockMode = null, $lockVersion = null)
 * @method ZombieType|null findOneBy(array $criteria, array $orderBy = null)
 * @method ZombieType[]    findAll()
 * @method ZombieType[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ZombieTypeRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ZombieType::class);
    }

    /**
     * @return ZombieType[]
     */
    public function findZombieTypesForRound(int|Game $round): array
    {
        if ($round instanceof Game) {
            $round = $round->getRound();
        }
        
        return $this->createQueryBuilder("zombieType")
            ->where("zombieType.maxRound > :ROUND")
            ->andWhere("zombieType.minRound <= :ROUND")
            ->setParameters([
                ":ROUND" => $round
            ])
            ->getQuery()
            ->getResult();
    }
}
