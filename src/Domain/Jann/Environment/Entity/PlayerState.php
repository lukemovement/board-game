<?php

namespace App\Domain\Jann\Environment\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\GamePlay\Entity\PlayerStat;
use App\Domain\Jann\Environment\Repository\PlayerStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerStateRepository::class)
 */
#[ApiResource]
class PlayerState
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $health;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxHealth;

    /**
     * @ORM\Column(type="integer")
     */
    private $attack;

    /**
     * @ORM\Column(type="integer")
     */
    private $energy;

    public function __construct(
        Player $player
    ) {
        $this->health = $player->getPlayerStat(PlayerStatConfig::HEALTH_ID)->getComputedLevel();        
        $this->maxHealth = $player->getPlayerStat(PlayerStatConfig::HEALTH_ID)->getPlayerStatConfig()->getMaxLevel();        
        $this->attack = $player->getPlayerStat(PlayerStatConfig::ATTACK_ID)->getComputedLevel();
        $this->energy = $player->getPlayerStat(PlayerStatConfig::ENERGY_ID)->getComputedLevel();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getHealth(): ?int
    {
        return $this->health;
    }

    public function setHealth(int $health): self
    {
        $this->health = $health;

        return $this;
    }

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(int $attack): self
    {
        $this->attack = $attack;

        return $this;
    }

    public function getMaxHealth(): ?int
    {
        return $this->maxHealth;
    }

    public function setMaxHealth(int $maxHealth): self
    {
        $this->maxHealth = $maxHealth;

        return $this;
    }

    public function getEnergy(): ?int
    {
        return $this->energy;
    }

    public function setEnergy(int $energy): self
    {
        $this->energy = $energy;

        return $this;
    }
}
