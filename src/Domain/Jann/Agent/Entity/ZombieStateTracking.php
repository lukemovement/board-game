<?php

namespace App\Domain\Jann\Agent\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\Jann\Agent\Repository\ZombieTrackingRepository;
use App\Domain\Jann\Environment\Entity\ZombieState;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ZombieStateTrackingRepository::class)
 */
#[ApiResource]
class ZombieStateTracking
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Zombie::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $zombie;

    /**
     * @ORM\ManyToOne(targetEntity=ZombieState::class)
     */
    private $zombieState;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getZombie(): ?Zombie
    {
        return $this->zombie;
    }

    public function setZombie(?Zombie $zombie): self
    {
        $this->zombie = $zombie;

        return $this;
    }

    public function getZombieState(): ?ZombieState
    {
        return $this->zombieState;
    }

    public function setZombieState(?ZombieState $zombieState): self
    {
        $this->zombieState = $zombieState;

        return $this;
    }
}
