<?php

namespace App\Domain\Jann\Environment\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Jann\Environment\Repository\RouteStateRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RouteStateRepository::class)
 */
#[ApiResource]
class RouteState
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=RouteTileLink::class, mappedBy="routeState", orphanRemoval=true)
     */
    private $tileLinks;

    /**
     * @ORM\Column(type="integer")
     */
    private $linkCount;
    
    public function __construct()
    {
        $this->tileLinks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|RouteTileLink[]
     */
    public function getTileLinks(): Collection
    {
        return $this->tileLinks;
    }

    public function addTileLink(RouteTileLink $tileLink): self
    {
        if (!$this->tileLinks->contains($tileLink)) {
            $this->tileLinks[] = $tileLink;
            $tileLink->setRouteState($this);
        }

        return $this;
    }

    public function removeTileLink(RouteTileLink $tileLink): self
    {
        if ($this->tileLinks->removeElement($tileLink)) {
            // set the owning side to null (unless already changed)
            if ($tileLink->getRouteState() === $this) {
                $tileLink->setRouteState(null);
            }
        }

        return $this;
    }

    public function getLinkCount(): ?int
    {
        return $this->linkCount;
    }

    public function setLinkCount(int $linkCount): self
    {
        $this->linkCount = $linkCount;

        return $this;
    }
}
