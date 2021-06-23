<?php

namespace App\Domain\Jann\Environment\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Common\Type\Position;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\Jann\Agent\Entity\Decision;
use App\Domain\Jann\Environment\Repository\TileStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TileStateRepository::class)
 */
#[ApiResource]
class TileState
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToMany(targetEntity=ZombieState::class, inversedBy="tileStates")
     */
    private $zombieStates;

    public function __construct()
    {
        
        $this->zombieStates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|ZombieState[]
     */
    public function getZombieStates(): Collection
    {
        return $this->zombieStates;
    }

    public function addZombieState(ZombieState $zombieState): self
    {
        if (!$this->zombieStates->contains($zombieState)) {
            $this->zombieStates[] = $zombieState;
        }

        return $this;
    }

    public function removeZombieState(ZombieState $zombieState): self
    {
        $this->zombieStates->removeElement($zombieState);

        return $this;
    }
}
