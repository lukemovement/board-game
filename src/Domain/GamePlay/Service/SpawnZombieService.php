<?php

declare(strict_types=1);

namespace App\Domain\GamePlay\Service;

use App\Domain\Common\Type\Position;
use App\Domain\GameData\Repository\ZombieTypeRepository;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GamePlay\Entity\Zombie;
use Doctrine\ORM\EntityManagerInterface;

class SpawnZombieService {

    public function __construct(
        private ZombieTypeRepository $zombieTypeRepository,
        private EntityManagerInterface $entityManager
    ) {}

    public function execute(Game $game)
    {
        if (false === $game->isDay()) {
            return;
        }

        $zombieCount = $game->getZombies()->count();
        $maxZombies = $game->getMaxZombies();
        $zombieTypes = $this->zombieTypeRepository->findZombieTypesForRound($game);

        for(;$zombieCount <= $maxZombies;$zombieCount++) {
            $zombie = new Zombie(
                $game,
                new Position([
                    rand(0, $game->getMap()->getColumns() - 1),
                    rand(0, $game->getMap()->getRows() - 1),
                ]),
                $zombieTypes[array_rand($zombieTypes)]
            );

            $game->addZombie($zombie);

        }
        
        $this->entityManager->persist($game);
        $this->entityManager->flush();
    }
}