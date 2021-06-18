<?php

namespace App\Domain\Jann\Environment\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Jann\Environment\Repository\RouteTileLinkRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=RouteTileLinkRepository::class)
 */
#[ApiResource]
class RouteTileLink
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TileState::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $tileState;

    /**
     * @ORM\ManyToOne(targetEntity=RouteState::class, inversedBy="tileLinks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $routeState;

    /**
     * @ORM\Column(type="integer")
     */
    private $linkCount;

    /**
     * @ORM\Column(type="integer")
     */
    private $position;

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

    public function getLinkCount(): ?int
    {
        return $this->linkCount;
    }

    public function setLinkCount(int $linkCount): self
    {
        $this->linkCount = $linkCount;

        return $this;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(int $position): self
    {
        $this->position = $position;

        return $this;
    }
}
