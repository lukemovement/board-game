<?php

namespace App\Domain\Jann\Environment\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Jann\Environment\Repository\TileRouteLinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TileRouteLinkRepository::class)
 */
#[ApiResource]
class TileRouteLink
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TileState::class, inversedBy="adjacentRouteLinks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tileState;

    /**
     * @ORM\ManyToOne(targetEntity=RouteState::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $routeState;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTileState(): ?TileState
    {
        return $this->tileState;
    }

    public function setTileState(?TileState $tileState): self
    {
        $this->tileState = $tileState;

        return $this;
    }

    public function getRouteState(): ?RouteState
    {
        return $this->routeState;
    }

    public function setRouteState(?RouteState $routeState): self
    {
        $this->routeState = $routeState;

        return $this;
    }
}
