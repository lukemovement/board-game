<?php

declare(strict_types=1);

namespace App\Domain\GamePlay\Service;

use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Entity\Player;

class MovePlayerService {

    public function execute(
        Player $player,
        MapTile|Position $position
    )
    {
        if ($position instanceof MapTile) {
            $position = $position->getPosition();
        }

        if ($player->getPosition()->matches($position)) {
            return;
        }

        $player->setPosition($position);
        $player->getPlayerStat(PlayerStatConfig::ENERGY_ID)->decrease(1);
        $player->getGame()->increaseMoveCount();
    }
}