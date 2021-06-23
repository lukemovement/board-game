<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\GameData\Entity\MapTile;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\Jann\Environment\Entity\RouteState;
use App\Domain\Jann\Environment\Entity\TileRouteLink;
use App\Domain\Jann\Environment\Entity\TileState;
use App\Domain\Jann\Environment\Entity\TileZombieLink;
use App\Domain\Jann\Environment\Entity\ZombieState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TileState|null find($id, $lockMode = null, $lockVersion = null)
 * @method TileState|null findOneBy(array $criteria, array $orderBy = null)
 * @method TileState[]    findAll()
 * @method TileState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TileStateRepository extends ServiceEntityRepository
{
    public function __construct(
        ManagerRegistry $registry
    )
    {
        parent::__construct($registry, TileState::class);
    }

    public function findOrCreate(ArrayCollection $zombies): TileState
    {
        $queryBuilder = $this->createQueryBuilder("tileState")
            ->join("tileState.zombieStates", "zombieState")
            ->where("zombieState.id IN (:ZOMBIE_LINKS)")
            ->andWhere("COUNT(zombieState) = (:ZOMBIE_LINKS_COUNT)");


        $queryBuilder->setParameters([
            ":ZOMBIE_LINKS" => $zombies,
            ":ZOMBIE_LINKS_COUNT" => $zombies->count(),
        ]);

        $matches = $queryBuilder->getQuery()->getResult();

        if (count($matches) > 0) {
            return $matches[0];
        }

        $tileState = new TileState();

        $zombies->forAll(fn(int $i, ZombieState $zombieState) => $tileState->addZombieState($zombieState));

        $this->_em->persist($tileState);
        $this->_em->flush($tileState);

        return $tileState;
    }
}
