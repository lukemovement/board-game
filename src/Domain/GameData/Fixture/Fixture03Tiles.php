<?php

namespace App\Domain\GameData\Fixture;

use App\Domain\GameData\Entity\Tile;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class Fixture03Tiles extends Fixture implements ORMFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        /** Straight */
        // top, bottom
        $manager->persist(
            new Tile(
                true,
                false,
                true,
                false
            )
        );

        // left, right
        $manager->persist(
            new Tile(
                false,
                true,
                false,
                true
            )
        );

        /** Corner */
        // top, right
        $manager->persist(
            new Tile(
                true,
                true,
                false,
                false
            )
        );

        // right, bottom
        $manager->persist(
            new Tile(
                false,
                true,
                true,
                false
            )
        );

        // bottom, left
        $manager->persist(
            new Tile(
                false,
                false,
                true,
                true
            )
        );

        // left, top
        $manager->persist(
            new Tile(
                true,
                false,
                false,
                true
            )
        );

        /** T Junction */
        // top, right, bottom
        $manager->persist(
            new Tile(
                true,
                true,
                true,
                false
            )
        );

        // right, bottom, left
        $manager->persist(
            new Tile(
                false,
                true,
                true,
                true
            )
        );

        // bottom, left, top
        $manager->persist(
            new Tile(
                true,
                false,
                true,
                true
            )
        );

        // left, top, right
        $manager->persist(
            new Tile(
                true,
                true,
                false,
                true
            )
        );

        // Cross roads
        $manager->persist(
            new Tile(
                true,
                true,
                true,
                true
            )
        );

        // Dead ends - top
        $manager->persist(
            new Tile(
                true,
                false,
                false,
                false
            )
        );

        // Dead ends - right
        $manager->persist(
            new Tile(
                false,
                true,
                false,
                false
            )
        );

        // Dead ends - bottom
        $manager->persist(
            new Tile(
                false,
                false,
                true,
                false
            )
        );

        // Dead ends - left
        $manager->persist(
            new Tile(
                false,
                false,
                false,
                true
            )
        );

        // Locked
        // $manager->persist(
        //     new Tile(
        //         false,
        //         false,
        //         false,
        //         false
        //     )
        // );

        $manager->flush();
    }
}
