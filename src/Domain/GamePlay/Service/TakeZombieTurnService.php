<?php

namespace App\Domain\GamePlay\Service;

use App\Domain\GamePlay\Dto\PathFinderNodeDto;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\GamePlay\Repository\PlayerRepository;
use App\Domain\GamePlay\Repository\ZombieRepository;

class TakeZombieTurnService {

    private Game $game;

    public function __construct(
        private ChanceGeneratorService $chanceGeneratorService,
        private ZombieRepository $zombieRepository,
        private PlayerRepository $playerRepository
    ) {}

    public function execute(
        Game $game
    )
    {
        $this->game = $game;

        $this->game->getZombies()->forAll(function(Zombie $zombie)
        {
            $localPlayers = $this->game->getPlayersAtPosition($zombie->getPosition());

            if ($localPlayers->isEmpty()) {
                $this->moveTowardsTarget($zombie);
                return;
            }

            $player = array_rand($localPlayers->toArray())[0];

            $zombie->attackPlayer($player);
            $this->playerRepository->add($player);
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
            ) => $a->route->count() < $b->route->count()
        );
            
        /** @var PathFinderNodeDto|null $nextNode */
        $nextNode = sizeof($movesToPlayers) !== 0 ? $movesToPlayers[0] : null;

        if (null !== $nextNode) {
            $zombie->setPosition($nextNode->destination->getPosition());
        } else {
            $shouldMove = $this->chanceGeneratorService->execute(1, 3);
    
            if ($shouldMove) {
                /** @var PathFinderNodeDto $nextNode */
                $nextNode = array_rand(
                    $this->game->map->getMapTile($zombie->getPosition())
                        ->getAdjacentTiles()
                        ->toArray()
                );
            }
    
            $zombie->setPosition($nextNode->destination->getPosition());
        }

        $this->zombieRepository->add($zombie);
    }
}