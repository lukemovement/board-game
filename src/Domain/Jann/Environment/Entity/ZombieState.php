<?php

namespace App\Domain\Jann\Environment\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GameData\Entity\ZombieType;
use App\Domain\Jann\Environment\Repository\ZombieStateRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ZombieStateRepository::class)
 */
#[ApiResource]
class ZombieState
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=ZombieType::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $zombieType;

    /**
     * @ORM\Column(type="integer")
     */
    private $health;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZombieType(): ?ZombieType
    {
        return $this->zombieType;
    }

    public function setZombieType(?ZombieType $zombieType): self
    {
        $this->zombieType = $zombieType;

        return $this;
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
}
