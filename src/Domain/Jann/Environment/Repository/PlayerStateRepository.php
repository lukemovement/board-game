<?php

namespace App\Domain\Jann\Environment\Repository;

use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\Jann\Environment\Entity\PlayerState;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PlayerState|null find($id, $lockMode = null, $lockVersion = null)
 * @method PlayerState|null findOneBy(array $criteria, array $orderBy = null)
 * @method PlayerState[]    findAll()
 * @method PlayerState[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PlayerStateRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PlayerState::class);
    }

    public function findOrCreate(
        Player $player
    )
    {
        $matches = $this->findBy([
            "health" => $player->getPlayerStat(PlayerStatConfig::HEALTH_ID),
            "energy" => $player->getPlayerStat(PlayerStatConfig::ENERGY_ID),            
            "attack" => $player->getPlayerStat(PlayerStatConfig::ATTACK_ID),
            "maxHealth" => $player->getPlayerStat(PlayerStatConfig::HEALTH_ID)->getPlayerStatConfig()->getMaxLevel()
        ]);

        if (count($matches) > 0) {
            return $matches[0];
        }

        $playerState = new PlayerState(
            $player
        );

        $this->_em->persist($playerState);
        $this->_em->flush();

        return $playerState;
    }
}
