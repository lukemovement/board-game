<?php

declare(strict_types=1);

namespace App\Domain\GamePlay\Service;

use App\Domain\Common\Type\Position;
use App\Domain\GameData\Repository\ZombieTypeRepository;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Zombie;

class SpawnZombieService {

    public function __construct(
        private ZombieTypeRepository $zombieTypeRepository
    ) {}

    public function execute(Game $game)
    {
        $zombieCount = $game->getZombies()->count();
        $maxZombies = $game->getMaxZombies();
        $zombieTypes = $this->zombieTypeRepository->findZombieTypesForRound($game);

        for(;$zombieCount <= $maxZombies;$zombieCount++) {
            $game->addZombie(new Zombie(
                $game,
                new Position([
                    rand(0, $game->getMap()->getColumns()),
                    rand(0, $game->getMap()->getRows()),
                ]),
                array_rand($zombieTypes)[0]
            ));
        }
    }
}