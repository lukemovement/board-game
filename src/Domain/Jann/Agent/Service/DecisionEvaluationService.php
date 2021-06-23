<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Service;

use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Dto\PathFinderNodeDto;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\Jann\Agent\Entity\Decision;
use App\Domain\Jann\Agent\Repository\DecisionRepository;
use App\Domain\Jann\Environment\Entity\PlayerState;
use App\Domain\Jann\Environment\Repository\PlayerStateRepository;
use App\Domain\Jann\Environment\Service\TileStateSetupService;
use App\Domain\Jann\NeuralNetworkConfig;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class DecisionEvaluationService {

    public function __construct(
        private DecisionRepository $decisionRepository,
        private PlayerStateRepository $playerStateRepository,
        private EntityManagerInterface $entityManager,
        private TileStateSetupService $tileStateSetupService
    ) {}

    private Player $player;

    /**
     * @return Decision[][]
     */
    public function execute(
        Player $player
    ): array
    {
        $this->player = $player;

        $playerState = $this->playerStateRepository->findOrCreate($player);

        $tileState = $this->tileStateRepository->findOrCreate(
            $this->player->getgame()->getZombiesAtPosition(
                $player->getPosition()
            )
        );

        $nextTileStates = $this->getAdjacentTiles();

        $availableDecisions = $this->decisionRepository->findAvailableMatches(
            $playerState,
            $tileState,
            $nextTileStates
        );

        return $this->walk($availableDecisions)->toArray();
    }

    /**
     * @param Decision[] $decision
     */
    public function walk(array $decisions, array $currentPath = [], ArrayCollection $allPaths = null, int $depth = 0)
    {
        if (NeuralNetworkConfig::ENVIRONMENT_SEARCH_DEPTH === $depth) {
            return $allPaths;
        }

        if (null === $allPaths) {
            $allPaths = new ArrayCollection();
        }

        $depth++;

        
        foreach($decisions as $decision) {
            // Create array for new decision pathway
            $newPath = $currentPath;
            $newPath[] = $decision;
            
            // Add new decision pathway to end result
            $allPaths->add($newPath);

            // Execute decision pathway
            array_walk($newPath, fn(Decision $decision) => $this->updateGame($decision));
            
            // Get the available decisions based on the new game state
            $outcomes = $this->predictOutComes($decision);
            
            // Reset the game state
            $this->entityManager->refresh($this->player);
            $this->entityManager->refresh($this->player->getGame());

            // Repeate for each outcome
            $this->walk($outcomes, $newPath, $allPaths, $depth);
        }

        return $allPaths;
    }

    /**
     * @param Decision[] $decisions
     */
    private function predictOutComes(Decision $decision): array|null
    {
        return $this->decisionRepository->findAvailableMatches(
            $decision->getNextPlayerState(),
            $decision->getMovedToTileState() ?? $decision->getCurrentTileState(),
            $this->getAdjacentTiles()
        );
    }

    private function updateGame(Decision $decision)
    {
        if (null !== $decision->getMovedToTileState()) {
            $currentMapTile = $this->player->getGame()->getMap()->getMapTile(
                $this->player->getPosition()
            );

            $currentMapTile->getAdjacentTiles()->forAll(function(MapTile $mapTile) use ($currentMapTile)
            {
                $nextTileState = $this->tileStateSetupService->execute(
                    $this->player->getGame(),
                    $this->player->getPosition()
                );

                if ($nextTileState->getId() === $currentMapTile->getId()) {
                    $this->player->setPosition($mapTile->getPosition());
                }
            });
        } else if (null !== $decision->getAttackedZombieStateAfter()) {
            if (
                $decision->getAttackedZombieStateBefore()->getCount() !==
                $decision->getAttackedZombieStateAfter()->getCount()
            ) {
                $this->player->getGame()->getZombiesAtPosition(
                    $this->player->getPosition()
                )->forAll(function(Zombie $zombie) use ($decision) {
                    if (
                        $zombie->getHealth() === $decision->getAttackedZombieStateBefore()->getHealth() &&
                        $zombie->getZombieType()->getId() === $decision->getAttackedZombieStateBefore()->getZombieType()->getId()
                    ) {
                        $this->player->getGame()->removeZombie($zombie);
                        return false;
                    }

                    return true;
                });
            }
        } else if (
            $decision->getAttackedZombieStateBefore()->getId() !==
            $decision->getAttackedZombieStateAfter()->getId()
        ) {
            $this->player->getGame()->getZombiesAtPosition(
                $this->player->getPosition()
            )->forAll(function(Zombie $zombie) use ($decision) {
                if (
                    $zombie->getHealth() === $decision->getAttackedZombieStateBefore()->getHealth() &&
                    $zombie->getZombieType()->getId() === $decision->getAttackedZombieStateBefore()->getZombieType()->getId()
                ) {
                    $zombie->setHealth(
                        $decision->getAttackedZombieStateAfter()->getHealth()
                    );
                    return false;
                }

                return true;
            });
        }
    }

    /**
     * @return ArrayCollection|TileState[]
     */
    private function getAdjacentTiles(): ArrayCollection
    {
        return $this->player->getgame()->getMap()->getMapTile(
            $this->player->getPosition()
        )->getAdjacentTiles()->map(fn(MapTile $mapTile) => $this->tileStateRepository->findOrCreate(
            $this->player->getgame()->getZombiesAtPosition(
                $mapTile->getPosition()
            )
        ));
    }
}