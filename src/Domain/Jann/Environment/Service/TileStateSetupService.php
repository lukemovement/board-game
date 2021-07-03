<?php

declare(strict_types=1);

namespace App\Domain\Jann\Environment\Service;

use App\Domain\Common\Type\Position;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\Jann\Environment\Entity\TileState;
use App\Domain\Jann\Environment\Entity\ZombieState;
use App\Domain\Jann\Environment\Repository\TileStateRepository;
use App\Domain\Jann\Environment\Repository\ZombieStateRepository;
use Doctrine\Common\Collections\ArrayCollection;

class TileStateSetupService {

    public function __construct(
        private ZombieStateRepository $zombieStateRepository,
        private TileStateRepository $tileStateRepository,
    ) {}

    public function execute(
        Game $game,
        Position $position
    ): TileState
    {
        $zombieStates = new ArrayCollection();

        $zombies = $game->getZombiesAtPosition(
            $position
        );

        foreach($zombies as $zombie) {
            $filteredZombieStates = $zombieStates->filter(
                fn(ZombieState $filterZombieState) => true === in_array(false, [
                    $filterZombieState->getHealth() === $zombie->getHealth(),
                    $filterZombieState->getZombieType()->getId() === $zombie->getZombieType()->getId(),
                ])
            );

            $count = $zombieStates->count() - $filteredZombieStates->count();

            if (0 === $count) {
                continue;
            }

            $zombieStates->add(
                $this->zombieStateRepository->findOrCreate($zombie, $count)
            );

            $zombieStates = $filteredZombieStates;
        }

        $tileState = $this->tileStateRepository->findOrCreate($zombieStates);

        
        return $tileState;
    }
}
