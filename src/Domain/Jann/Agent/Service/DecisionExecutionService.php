<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Service;

use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\Jann\Agent\Dto\DecisionDto;

class DecisionExecutionService {

    public function execute(
        DecisionDto $decision,
        Player $player
    ) {
        $game = $player->getGame();

        switch(true) {
            case $decision->behaviour->isTypeMove():
                $mapTile = $player->getGame()->getMap()->getMapTile(
                    $player->getPosition()
                );

                $mapTile->getAdjacentTiles()->forAll(function($adjecentMapTile) use ($player, $game, $decision) {
                    $tileStateMatch = $this->tileStateSetupService->execute(
                        $game,
                        $adjecentMapTile->getPosition()
                    );

                    if ($tileStateMatch->getId() === $decision->behaviour->getPreviousTileState()) {
                        $this->movePlayerService->execute($player, $adjecentMapTile);
                        return false;
                    }
                    return true;
                });

                break;
            case $decision->behaviour->isTypeAttack():
                $game->getZombiesAtPosition(
                    $player->getPosition()
                )->forAll(function(Zombie $zombie) use ($player, $decision) {
                    $zombieStateMatch = $this->zombieStateRepository->findOrCreate($zombie);
                    
                    if ($decision->behaviour->getAttackedZombieStateBefore()->getId() === $zombieStateMatch->getId()) {
                        $this->playerAttackZombieService->execute($player, $zombie);
                        return false;
                    }
                    return true;
                });
                break;

        }

    }
}