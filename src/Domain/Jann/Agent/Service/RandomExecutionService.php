<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Service;

use App\Domain\GameData\Entity\MapTile;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\GamePlay\Service\ChanceGeneratorService;
use App\Domain\GamePlay\Service\MovePlayerService;
use App\Domain\Jann\Agent\Dto\DecisionDto;
use App\Domain\Jann\Behaviour\Repository\BehaviourRepository;
use App\Domain\Jann\Environment\Repository\PlayerStateRepository;
use App\Domain\Jann\Environment\Repository\ZombieStateRepository;
use App\Domain\Jann\Environment\Service\TileStateSetupService;

class RandomExecutionService
{
    public function __construct(
        private ZombieStateRepository $zombieStateRepository,
        private MovePlayerService $movePlayerService,
        private TileStateSetupService $tileStateSetupService,
        private PlayerStateRepository $playerStateRepository,
        private BehaviourRepository $behaviourRepository
    ) {}

    public function execute(
        Player $player
    ): void {
        $game = $player->getGame();

        $mapTile = $player->getGame()->getMap()->getMapTile(
            $player->getPosition()
        );

        $decisionIndex = rand(
            0,
            $mapTile->getAdjacentTiles()->count() + $game->getZombiesAtPosition(
                $player->getPosition()
            )->count()
        );
        
        $previousTileState = $this->tileStateSetupService->execute($player->getGame(), $player->getPosition());
        $previousPlayerState = $this->playerStateRepository->findOrCreate($player);

        $movedToTileState = null;
        $previousZombieState = null;
        $nextZombieState = null;

        if ($decisionIndex < $mapTile->getAdjacentTiles()->count()) {
            $mapTile->getAdjacentTiles()->forAll(
                function (int $index, MapTile $adjecentMapTile) use (&$movedToTileState, $player, $game, $decisionIndex) {
                    if ($index !== $decisionIndex) {
                        return true;
                    }

                    $this->tileStateSetupService->execute(
                        $game,
                        $adjecentMapTile->getPosition()
                    );

                    $this->movePlayerService->execute($player, $adjecentMapTile);

                    $movedToTileState = $this->tileStateSetupService->execute($player->getGame(), $player->getPosition());

                    return false;
                }
            );
        } else {
            $decisionIndex = $decisionIndex - ($mapTile->getAdjacentTiles()->count() - 1);
    
            $game->getZombiesAtPosition(
                $player->getPosition()
            )->forAll(
                function (int $index, Zombie $zombie) use (&$previousZombieState, &$nextZombieState, $player, $decisionIndex) {
                    if ($index !== $decisionIndex) {
                        return true;
                    }

                    $previousZombieState = $this->zombieStateRepository->findOrCreate($zombie);
        
                    $this->zombieStateRepository->findOrCreate($zombie);
        
                    $this->playerAttackZombieService->execute($player, $zombie);

                    $nextZombieState = $this->zombieStateRepository->findOrCreate($zombie);
        
                    return false;
                }
            );
        }

        $nextPlayerState = $this->playerStateRepository->findOrCreate($player);

        $this->behaviourRepository->createOrIncreaseLinkCount(
            $previousTileState,
            $movedToTileState,
            $previousPlayerState,
            $nextPlayerState,
            $previousZombieState,
            $nextZombieState
        );
    }
}
