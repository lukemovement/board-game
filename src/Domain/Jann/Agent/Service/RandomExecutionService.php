<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Service;

use App\Domain\GameData\Entity\MapTile;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\GamePlay\Service\ChanceGeneratorService;
use App\Domain\GamePlay\Service\MovePlayerService;
use App\Domain\GamePlay\Service\PlayerAttackZombieService;
use App\Domain\Jann\Agent\Dto\DecisionDto;
use App\Domain\Jann\Behaviour\Repository\BehaviourRepository;
use App\Domain\Jann\Environment\Repository\PlayerStateRepository;
use App\Domain\Jann\Environment\Repository\ZombieStateRepository;
use App\Domain\Jann\Environment\Service\TileStateSetupService;
use Throwable;

class RandomExecutionService
{
    public function __construct(
        private ZombieStateRepository $zombieStateRepository,
        private MovePlayerService $movePlayerService,
        private TileStateSetupService $tileStateSetupService,
        private PlayerStateRepository $playerStateRepository,
        private BehaviourRepository $behaviourRepository,
        private PlayerAttackZombieService $playerAttackZombieService
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
            )->count() - 1
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
            $decisionIndex = $decisionIndex - ($mapTile->getAdjacentTiles()->count() + 1);
            $game->getZombiesAtPosition(
                $player->getPosition()
            )->forAll(
                function (int $index, Zombie $zombie) use (&$previousZombieState, &$nextZombieState, $player, $decisionIndex) {
                    if ($index !== $decisionIndex) {
                        return true;
                    }

                    $count = $player->getGame()->getZombiesAtPosition($player->getPosition())->filter(
                        fn(Zombie $zombieMatch) => false === in_array(false, [
                            $zombieMatch->getZombieType()->getId() === $zombie->getZombieType()->getId(),
                            $zombieMatch->getHealth() === $zombie->getHealth(),
                        ])
                    )->count();

                    $previousZombieState = $this->zombieStateRepository->findOrCreate($zombie, $count);
        
                    $this->playerAttackZombieService->execute($player, $zombie);

                    $count = $player->getGame()->getZombiesAtPosition($player->getPosition())->filter(
                        fn(Zombie $zombieMatch) => false === in_array(false, [
                            $zombieMatch->getZombieType()->getId() === $zombie->getZombieType()->getId(),
                            $zombieMatch->getHealth() === $zombie->getHealth(),
                        ])
                    )->count();

                    $nextZombieState = $this->zombieStateRepository->findOrCreate($zombie, $count);
        
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