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
     * @ORM\OneToMany(targetEntity=TileZombieLink::class, mappedBy="tileState", orphanRemoval=true)
     */
    private $zombieLinks;

    /**
     * @ORM\OneToMany(targetEntity=TileRouteLink::class, mappedBy="tileState", orphanRemoval=true)
     */
    private $adjacentRouteLinks;

    /**
     * @ORM\OneToMany(targetEntity=Decision::class, mappedBy="previousTileState", orphanRemoval=true)
     */
    private $nextDecisions;

    /**
     * @ORM\OneToMany(targetEntity=Decision::class, mappedBy="nextTileState")
     */
    private $previousDecisions;

    public function __construct()
    {
        
        $this->zombieLinks = new ArrayCollection();
        $this->adjacentRouteLinks = new ArrayCollection();
        $this->predictedDecisions = new ArrayCollection();
        $this->nextDecisions = new ArrayCollection();
        $this->previousDecisions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|TileZombieLink[]
     */
    public function getZombieLinks(): Collection
    {
        return $this->zombieLinks;
    }

    public function addZombieLink(TileZombieLink $zombieLink): self
    {
        if (!$this->zombieLinks->contains($zombieLink)) {
            $this->zombieLinks[] = $zombieLink;
            $zombieLink->setTileState($this);
        }

        return $this;
    }

    public function removeZombieLink(TileZombieLink $zombieLink): self
    {
        if ($this->zombieLinks->removeElement($zombieLink)) {
            // set the owning side to null (unless already changed)
            if ($zombieLink->getTileState() === $this) {
                $zombieLink->setTileState(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|TileRouteLink[]
     */
    public function getAdjacentRouteLinks(): Collection
    {
        return $this->adjacentRouteLinks;
    }

    public function addAdjacentRouteLink(TileRouteLink $tileRouteLink): self
    {
        if (!$this->adjacentRouteLinks->contains($tileRouteLink)) {
            $this->adjacentRouteLinks[] = $tileRouteLink;
            $tileRouteLink->setTileState($this);
        }

        return $this;
    }

    public function removeAdjacentRouteLink(TileRouteLink $tileRouteLink): self
    {
        if ($this->adjacentRouteLinks->removeElement($tileRouteLink)) {
            // set the owning side to null (unless already changed)
            if ($tileRouteLink->getTileState() === $this) {
                $tileRouteLink->setTileState(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Decision[]
     */
    public function getNextDecisions(): Collection
    {
        return $this->nextDecisions;
    }

    public function addNextDecision(Decision $nextDecision): self
    {
        if (!$this->nextDecisions->contains($nextDecision)) {
            $this->nextDecisions[] = $nextDecision;
            $nextDecision->setPreviousTileState($this);
        }

        return $this;
    }

    public function removeNextDecision(Decision $nextDecision): self
    {
        if ($this->nextDecisions->removeElement($nextDecision)) {
            // set the owning side to null (unless already changed)
            if ($nextDecision->getPreviousTileState() === $this) {
                $nextDecision->setPreviousTileState(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Decision[]
     */
    public function getPreviousDecisions(): Collection
    {
        return $this->previousDecisions;
    }

    public function addPreviousDecision(Decision $previousDecision): self
    {
        if (!$this->previousDecisions->contains($previousDecision)) {
            $this->previousDecisions[] = $previousDecision;
            $previousDecision->setNextTileState($this);
        }

        return $this;
    }

    public function removePreviousDecision(Decision $previousDecision): self
    {
        if ($this->previousDecisions->removeElement($previousDecision)) {
            // set the owning side to null (unless already changed)
            if ($previousDecision->getNextTileState() === $this) {
                $previousDecision->setNextTileState(null);
            }
        }

        return $this;
    }
}
