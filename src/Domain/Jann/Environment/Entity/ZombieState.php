<?php

namespace App\Domain\Jann\Environment\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GameData\Entity\ZombieType;
use App\Domain\Jann\Environment\Repository\ZombieStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    /**
     * @ORM\ManyToMany(targetEntity=TileState::class, mappedBy="zombieStates")
     */
    private $tileStates;

    /**
     * @ORM\Column(type="integer")
     */
    private $count;

    public function __construct()
    {
        $this->tileStates = new ArrayCollection();
    }

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

    /**
     * @return Collection|TileState[]
     */
    public function getTileStates(): Collection
    {
        return $this->tileStates;
    }

    public function addTileState(TileState $tileState): self
    {
        if (!$this->tileStates->contains($tileState)) {
            $this->tileStates[] = $tileState;
            $tileState->addZombieState($this);
        }

        return $this;
    }

    public function removeTileState(TileState $tileState): self
    {
        if ($this->tileStates->removeElement($tileState)) {
            $tileState->removeZombieState($this);
        }

        return $this;
    }

    public function getCount(): ?int
    {
        return $this->count;
    }

    public function setCount(int $count): self
    {
        $this->count = $count;

        return $this;
    }
}
