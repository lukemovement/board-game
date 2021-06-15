<?php

namespace App\Domain\GameData\Fixture;

use App\Domain\GameData\Entity\Item;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GameData\Entity\PlayerStatModifier;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Bundle\FixturesBundle\ORMFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class Fixture02Items extends Fixture implements ORMFixtureInterface
{
    private ObjectManager $manager;

    public function load(ObjectManager $manager)
    {
        $this->manager = $manager;

        $this->loadSharpWeapons();

        $manager->flush();
    }

    private function loadSharpWeapons()
    {
        $attackStat = $this->manager->find(PlayerStatConfig::class, Fixture01PlayerStatConfigs::$playerAttackStatReference);

        /** Blunt knife */
        $bluntKnife = new Item();
        $bluntKnife->setName("Blunt knife");
        $bluntKnife->setMinRound(0);
        $bluntKnife->setMaxRound(7);
        $bluntKnife->setDurability(20);
        $bluntKnife->setUseOnAttack(true);
        $bluntKnife->setUseOnDefence(true);
        $bluntKnife->setUseFromBackpack(false);
        $bluntKnife->setRightHandSlot(true);
        $this->manager->persist($bluntKnife);

        $attackModifier = new PlayerStatModifier(
            $attackStat,
            1
        );
        $bluntKnife->addModifier($attackModifier);
        $this->manager->persist($attackModifier);

        /** Sharp knife */
        $sharpKnife = new Item();
        $sharpKnife->setName("Sharp knife");
        $sharpKnife->setMinRound(5);
        $sharpKnife->setMaxRound(12);
        $sharpKnife->setDurability(20);
        $sharpKnife->setUseOnAttack(true);
        $sharpKnife->setUseOnDefence(true);
        $sharpKnife->setUseFromBackpack(false);
        $sharpKnife->setRightHandSlot(true);
        $this->manager->persist($sharpKnife);

        $attackModifier = new PlayerStatModifier(
            $attackStat,
            2
        );
        $sharpKnife->addModifier($attackModifier);
        $this->manager->persist($attackModifier);

        /** Machete */
        $machete = new Item();
        $machete->setName("Machete");
        $machete->setMinRound(10);
        $machete->setMaxRound(17);
        $machete->setDurability(20);
        $machete->setUseOnAttack(true);
        $machete->setUseOnDefence(true);
        $machete->setRightHandSlot(true);
        $machete->setUseFromBackpack(false);
        $this->manager->persist($machete);

        $attackModifier = new PlayerStatModifier(
            $attackStat,
            3
        );
        $machete->addModifier($attackModifier);
        $this->manager->persist($attackModifier);
    }
}
