<?php

namespace App\Domain\GameData\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Common\Type\Position;
use App\Domain\GameData\Repository\MapTileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Throwable;

/**
 * @ORM\Entity(repositoryClass=MapTileRepository::class)
 */
#[ApiResource]
class MapTile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Map::class, inversedBy="mapTiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $map;

    /**
     * @var Position
     * @ORM\Column(type="position")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity=Tile::class, inversedBy="mapTiles")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tile;

    public function __construct(
        Map $map,
        Tile $tile,
        int $columnIndex,
        int $rowIndex
    ) {
        $this->map = $map;
        $this->tile = $tile;
        $this->position = new Position([$columnIndex, $rowIndex]);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): self
    {
        $this->map = $map;

        return $this;
    }

    public function getPosition(): ?Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return ArrayCollection|MapTile[]
     */
    public function getAdjacentTiles(): ArrayCollection
    {
        $tiles = new ArrayCollection();

        if ($this->tile->getCanExitTop()) {
            $tiles->add($this->map->getMapTile($this->position->up()));
        }
        
        if ($this->tile->getCanExitLeft()) {
            $tiles->add($this->map->getMapTile($this->position->left()));
        }

        if ($this->tile->getCanExitRight()) {
            $tiles->add($this->map->getMapTile($this->position->right()));
        }

        if ($this->tile->getCanExitBottom()) {
            $tiles->add($this->map->getMapTile($this->position->down()));
        }
        
        return $tiles;
    }

    public function getTile(): ?Tile
    {
        return $this->tile;
    }

    public function setTile(?Tile $tile): self
    {
        $this->tile = $tile;

        return $this;
    }
}
