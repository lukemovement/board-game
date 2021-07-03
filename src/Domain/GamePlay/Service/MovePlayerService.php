<?php

declare(strict_types=1);

namespace App\Domain\GamePlay\Service;

use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\MapTile;
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

        $player->setPosition($position);
    }
}