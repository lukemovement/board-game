<?php

namespace App\Domain\GamePlay\Service;

use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Interface\MovableInterface;
use Exception;

class MoveNpcService {

    public const TARGET_PLAYER = 0;
    public const TARGET_ZOMBIE = 1;
    public const TARGET_SEARCH = 2;

    public function __construct(
        private PathFinderService $moveTreeSerive,
        private ChanceGeneratorService $chanceGeneratorService
    ) {}

    public function execute(
        Game $game,
        MovableInterface $npc,
        int $target,
        bool $mustMove
    )
    {
        $map = $game->getMap();
        $position = $npc->getPosition();

        $potentialMoves = $this->moveTreeSerive->execute(
            $map->getMapTile($position),
            $map->getZombieVisibility()
        )->filter(
            function(PathFinderNodeDto $node) use ($game, $target) {
                switch($target) {
                    case self::TARGET_PLAYER;
                        return false === $game->getPlayersAtPosition(
                            $node->destination->getPosition()
                        )->isEmpty();
                    break;
                    case self::TARGET_ZOMBIE;
                        return false === $game->getZombiesAtPosition(
                            $node->destination->getPosition()
                        )->isEmpty();
                    break;
                    case self::TARGET_SEARCH;
                        return false === $game->getSearchableInteractionsPosition(
                            $node->destination->getPosition()
                        )->isEmpty();
                    break;
                    default:
                        throw new Exception("Target width id $target not recognised");
                    break;
                }

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
            $npc->setPosition($nextNode->destination->getPosition());
            return;
        }

        $shouldMove = $this->chanceGeneratorService->execute(1, 3);

        if ($shouldMove || $mustMove) {
            /** @var PathFinderNodeDto|null $nextNode */
            $nextNode = array_rand(
                $map->getMapTile($npc->getPosition())
                    ->getAdjacentTiles()
                    ->toArray()
            );

            $npc->setPosition($nextNode->destination->getPosition());
        }
    }
}