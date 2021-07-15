<?php

declare(strict_types=1);

namespace App\Domain\GamePlay\Service;

use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\GamePlay\GamePlayConfig;

class PlayerAttackZombieService {

    public function __construct(
        private ChanceGeneratorService $chanceGeneratorService
    ) {}

    public function execute(
        Player $player,
        Zombie $zombie
    )
    {
        $playerAttackModifier = $player->getPlayerStat(PlayerStatConfig::ATTACK_ID)->getComputedLevel();

        $playerAttackChance = $this->chanceGeneratorService->execute(GamePlayConfig::PLAYER_ATTACK_MIN, GamePlayConfig::PLAYER_ATTACK_MAX);

        $playerAttack = $playerAttackModifier + $playerAttackChance;

        if ($zombie->getHealth() <= $playerAttack) {
            $player->getGame()->removeZombie($zombie);
        } else {
            $zombie->setHealth(
                $zombie->getHealth() - $playerAttack
            );
        }

        $player->getPlayerStat(PlayerStatConfig::ENERGY_ID)->decrease(1);
        $player->getGame()->increaseMoveCount();
    }

}