<?php

declare(strict_types=1);

namespace App\Domain\Jann\Behaviour\Service;

use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Dto\PathFinderNodeDto;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\Jann\Behaviour\Entity\Behaviour;
use App\Domain\Jann\Behaviour\Repository\BehaviourRepository;
use App\Domain\Jann\Environment\Entity\PlayerState;
use App\Domain\Jann\Environment\Repository\PlayerStateRepository;
use App\Domain\Jann\Environment\Service\TileStateSetupService;
use App\Domain\Jann\NeuralNetworkConfig;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;

class BehaviourPredictionService {

    public function __construct(
        private BehaviourRepository $behaviourRepository,
        private PlayerStateRepository $playerStateRepository,
        private EntityManagerInterface $entityManager,
        private TileStateSetupService $tileStateSetupService
    ) {}

    private Player $player;

    /**
     * @return Behaviour[][]
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

        $availableBehaviours = $this->behaviourRepository->findAvailableMatches(
            $playerState,
            $tileState,
            $nextTileStates
        );

        return $this->walk($availableBehaviours);
    }

    /**
     * @param Behaviour[] $behaviour
     * 
     * @return Behaviour[][]
     */
    public function walk(array $behaviours, array $currentPath = [], ArrayCollection $allPaths = null, int $depth = 0): array
    {
        if (NeuralNetworkConfig::ENVIRONMENT_SEARCH_DEPTH === $depth) {
            return $allPaths->toArray();
        }

        if (null === $allPaths) {
            $allPaths = new ArrayCollection();
        }

        $depth++;
        
        foreach($behaviours as $behaviour) {
            // Create array for new behaviour pathway
            $newPath = $currentPath;
            $newPath[] = $behaviour;
            
            // Add new behaviour pathway to end result
            $allPaths->add($newPath);

            // Execute behaviour pathway
            array_walk($newPath, fn(Behaviour $behaviour) => $this->updateGame($behaviour));
            
            // Get the available behaviours based on the new game state
            $outcomes = $this->predictOutComes($behaviour);
            
            // Reset the game state
            $this->entityManager->refresh($this->player);
            $this->entityManager->refresh($this->player->getGame());

            // Repeate for each outcome
            $this->walk($outcomes, $newPath, $allPaths, $depth);
        }

        return $allPaths->toArray();
    }

    /**
     * @param Behaviour[] $behaviours
     */
    private function predictOutComes(Behaviour $behaviour): array|null
    {
        return $this->behaviourRepository->findAvailableMatches(
            $behaviour->getNextPlayerState(),
            $behaviour->getMovedToTileState() ?? $behaviour->getLevelTileState(),
            $this->getAdjacentTiles()
        );
    }

    private function updateGame(Behaviour $behaviour)
    {
        if (null !== $behaviour->getMovedToTileState()) {
            // Player moved to a new tile
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
        } else if (null !== $behaviour->getAttackedZombieStateAfter()) {
            // Player killed a zombie
            if (
                $behaviour->getAttackedZombieStateBefore()->getCount() !==
                $behaviour->getAttackedZombieStateAfter()->getCount()
            ) {
                $this->player->getGame()->getZombiesAtPosition(
                    $this->player->getPosition()
                )->forAll(function(Zombie $zombie) use ($behaviour) {
                    if (
                        $zombie->getHealth() === $behaviour->getAttackedZombieStateBefore()->getHealth() &&
                        $zombie->getZombieType()->getId() === $behaviour->getAttackedZombieStateBefore()->getZombieType()->getId()
                    ) {
                        $this->player->getGame()->removeZombie($zombie);
                        return false;
                    }

                    return true;
                });
            }
        } else if (
            $behaviour->getAttackedZombieStateBefore()->getId() !==
            $behaviour->getAttackedZombieStateAfter()->getId()
        ) {
            // Player attacked a zombie and decreased its health
            $this->player->getGame()->getZombiesAtPosition(
                $this->player->getPosition()
            )->forAll(function(Zombie $zombie) use ($behaviour) {
                if (
                    $zombie->getHealth() === $behaviour->getAttackedZombieStateBefore()->getHealth() &&
                    $zombie->getZombieType()->getId() === $behaviour->getAttackedZombieStateBefore()->getZombieType()->getId()
                ) {
                    $zombie->setHealth(
                        $behaviour->getAttackedZombieStateAfter()->getHealth()
                    );
                    return false;
                }

                return true;
            });
        }

        $this->player->getPlayerStat(PlayerStatConfig::ENERGY_ID)->decrease(1);
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