<?php

namespace App\Domain\GameData\Fixture;

use App\Domain\GameData\Entity\Tile;
use App\Domain\GameData\Entity\ZombieType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class Fixture04ZombieTypes extends Fixture implements ORMFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $manager->persist(
            new ZombieType(
                "Walker",
                "",
                2,
                4,
                1,
                5,
                0,
                100
            )  
        );

        $manager->flush();
    }
}
