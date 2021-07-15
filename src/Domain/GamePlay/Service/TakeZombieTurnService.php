<?php

namespace App\Domain\GamePlay\Service;

use App\Domain\GameData\Entity\MapTile;
use App\Domain\GamePlay\Dto\PathFinderNodeDto;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\GamePlay\GamePlayConfig;
use App\Domain\GamePlay\Repository\PlayerRepository;
use App\Domain\GamePlay\Repository\ZombieRepository;
use Exception;
use Throwable;

class TakeZombieTurnService {

    private Game $game;

    public function __construct(
        private ChanceGeneratorService $chanceGeneratorService,
    ) {}

    public function execute(
        Game $game
    )
    {
        $this->game = $game;

        $this->game->getZombies()->forAll(function(int $i, Zombie $zombie)
        {
            $localPlayers = $this->game->getPlayersAtPosition($zombie->getPosition());

            if ($localPlayers->isEmpty()) {
                $this->moveTowardsTarget($zombie);
                return true;
            }

            $player = $localPlayers[array_rand($localPlayers->toArray())];

            $zombie->attackPlayer($player);

            return true;
        });
    }

    private function moveTowardsTarget(
        Zombie $zombie
    )
    {
        $potentialMoves = $this->game->getAvailableRoutes(
            $zombie->getMapTile(),
            $this->game->getMap()->getZombieVisibility()
        )->filter(
            function(PathFinderNodeDto $node) {
                return false === $this->game->getPlayersAtPosition(
                    $node->destination->getPosition()
                )->isEmpty();
            } 
        );

        $movesToPlayers = $potentialMoves->toArray();
        usort(
            $movesToPlayers,
            fn(
                PathFinderNodeDto $a,
                PathFinderNodeDto $b
            ) => $a->route->count() === $b->route->count() ? 0 : ($a->route->count() > $b->route->count() ? 1 : -1)
        );
            
        /** @var PathFinderNodeDto|null $playerRoute */
        $playerRoute = sizeof($movesToPlayers) !== 0 ? $movesToPlayers[0] : null;

        if (null !== $playerRoute) {
            if ($playerRoute->route->isEmpty()) {
                $position = $playerRoute->destination->getPosition();
            } else {
                /** @var PathFinderNodeDto|null $firstStep */
                $firstStep = $playerRoute->route->get(0);
                $position = $firstStep->destination->getPosition();
            }

            $zombie->setPosition($position);
            return;
        }

        $shouldMove = $this->chanceGeneratorService->execute(GamePlayConfig::ZOMBIE_MOVE_CHANCE, GamePlayConfig::ZOMBIE_MOVE_CHANCE_OUTOF);

        if ($shouldMove) {
            /** @var MapTile[] $availableMoves */
            $availableMoves = $this->game->getMap()->getMapTile($zombie->getPosition())
                ->getAdjacentTiles()
                ->toArray();
                
            $nextNodeIndex = array_rand(
                $availableMoves
            );

            $mapTile = $availableMoves[$nextNodeIndex];

            $zombie->setPosition($mapTile->getPosition());
        }
    }
}