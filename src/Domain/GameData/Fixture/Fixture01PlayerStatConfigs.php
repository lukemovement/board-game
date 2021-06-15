<?php

namespace App\Domain\GameData\Fixture;

use App\Domain\GameData\Entity\PlayerStatConfig;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class Fixture01PlayerStatConfigs extends Fixture implements ORMFixtureInterface
{
    public static $playerAttackStatReference = null;
    public static $playerHealthStatReference = null;
    public static $playerEnergyStatReference = null;

    public function load(ObjectManager $manager)
    {
        $attack = new PlayerStatConfig(
            PlayerStatConfig::ATTACK_ID,
            "Attack",
            0,
            0,
            0,
            1
        );
        $manager->persist($attack);
        
        $health = new PlayerStatConfig(
            PlayerStatConfig::HEALTH_ID,
            "Health",
            10,
            10,
            1,
            1
        );
        $manager->persist($health);
        
        $energy = new PlayerStatConfig(
            PlayerStatConfig::ENERGY_ID,
            "Energy",
            5,
            3,
            3,
            1
        );
        $manager->persist($energy);
        
        $manager->flush();
        
        self::$playerAttackStatReference = $attack->getId();
        $this->addReference(self::$playerAttackStatReference, $attack);
        self::$playerHealthStatReference = $health->getId();
        $this->addReference(self::$playerHealthStatReference, $health);
        self::$playerEnergyStatReference = $energy->getId();
        $this->addReference(self::$playerEnergyStatReference, $energy);
    }
}
