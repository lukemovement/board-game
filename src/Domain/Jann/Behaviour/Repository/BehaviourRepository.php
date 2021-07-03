<?php

namespace App\Domain\Jann\Behaviour\Repository;

use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\Jann\Behaviour\Entity\Behaviour;
use App\Domain\Jann\Environment\Entity\PlayerState;
use App\Domain\Jann\Environment\Entity\TileState;
use App\Domain\Jann\Environment\Entity\ZombieState;
use App\Domain\Jann\Environment\Repository\PlayerStateRepository;
use App\Domain\Jann\Environment\Repository\TileStateRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Behaviour|null find($id, $lockMode = null, $lockVersion = null)
 * @method Behaviour|null findOneBy(array $criteria, array $orderBy = null)
 * @method Behaviour[]    findAll()
 * @method Behaviour[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BehaviourRepository extends ServiceEntityRepository
{
    public function __construct(
        private PlayerStateRepository $playerStateRepository,
        private TileStateRepository $tileStateRepository,
        ManagerRegistry $registry
    ) {
        parent::__construct($registry, Behaviour::class);
    }

    /**
     * @return Behaviour[]
     */
    public function findAvailableMatches(
        PlayerState $playerState,
        TileState $tileState,
        ArrayCollection $adjacentTileStates
    ): array
    {
        return $this->createQueryBuilder("d")
            ->where("d.previousPlayerState = :PLAYER_STATE")
            ->andWhere("d.currentTileState = :TILE_STATE")
            ->andWhere("d.movedToTileState IN (:NEXT_TILE_STATES)")
            ->orWhere("d.attackedZombieStateBefore IN (:ZOMBIE_STATES)")
            ->andWhere("d.previousPlayerState = :PLAYER_STATE")
            ->andWhere("d.currentTileState = :TILE_STATE")
            ->setParameters([
                ":PLAYER_STATE" => $playerState,
                ":TILE_STATE" => $tileState,
                ":NEXT_TILE_STATES" => $adjacentTileStates,
                ":ZOMBIE_STATES" => $tileState->getZombieStates()
            ])
            ->getQuery()
            ->getResult();
    }

    public function createOrIncreaseLinkCount(
        TileState $currentTileState,
        ?TileState $movedToTileState,
        PlayerState $previousPlayerState,
        PlayerState $nextPlayerState,
        ?ZombieState $attackedZombieStateBefore,
        ?ZombieState $attackedZombieStateAfter,
    ): Behaviour
    {
        $match = $this->findOneBy([
            "currentTileState" => $currentTileState,
            "movedToTileState" => $movedToTileState,
            "previousPlayerState" => $previousPlayerState,
            "nextPlayerState" => $nextPlayerState,
            "attackedZombieStateBefore" => $attackedZombieStateBefore,
            "attackedZombieStateAfter" => $attackedZombieStateAfter,
        ]);

        if (null === $match) {
            $match = new Behaviour(
                $currentTileState,
                $movedToTileState,
                $previousPlayerState,
                $nextPlayerState,
                $attackedZombieStateBefore,
                $attackedZombieStateAfter,
            );
        }

        $match->increaseLinkCount();

        $this->_em->persist($match);
        $this->_em->flush();

        return $match;
    }
}
