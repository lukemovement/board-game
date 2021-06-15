<?php

namespace App\Domain\GameData\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Common\Type\Position;
use App\Domain\GameData\Repository\TileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TileRepository::class)
 */
#[ApiResource]
class Tile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="boolean")
     */
    private $canExitLeft;

    /**
     * @ORM\Column(type="boolean")
     */
    private $canExitRight;

    /**
     * @ORM\Column(type="boolean")
     */
    private $canExitTop;

    /**
     * @ORM\Column(type="boolean")
     */
    private $canExitBottom;

    /**
     * @ORM\OneToMany(targetEntity=MapTile::class, mappedBy="tile", orphanRemoval=true)
     */
    private $mapTiles;

    public function __construct(
        bool $canExitTop,
        bool $canExitRight,
        bool $canExitBottom,
        bool $canExitLeft,
    ) {
        $this->canExitTop = $canExitTop;
        $this->canExitRight = $canExitRight;
        $this->canExitBottom = $canExitBottom;
        $this->canExitLeft = $canExitLeft;
        $this->mapTiles = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCanExitLeft(): bool
    {
        return $this->canExitLeft;
    }

    public function setCanExitLeft(bool $canExitLeft): self
    {
        $this->canExitLeft = $canExitLeft;

        return $this;
    }

    public function getCanExitRight(): bool
    {
        return $this->canExitRight;
    }

    public function setCanExitRight(bool $canExitRight): self
    {
        $this->canExitRight = $canExitRight;

        return $this;
    }

    public function getCanExitTop(): bool
    {
        return $this->canExitTop;
    }

    public function setCanExitTop(bool $canExitTop): self
    {
        $this->canExitTop = $canExitTop;

        return $this;
    }

    public function getCanExitBottom(): bool
    {
        return $this->canExitBottom;
    }

    public function setCanExitBottom(bool $canExitBottom): self
    {
        $this->canExitBottom = $canExitBottom;

        return $this;
    }

    /**
     * @return Collection|MapTile[]
     */
    public function getMapTiles(): Collection
    {
        return $this->mapTiles;
    }

    public function addMapTile(MapTile $mapTile): self
    {
        if (!$this->mapTiles->contains($mapTile)) {
            $this->mapTiles[] = $mapTile;
            $mapTile->setTile($this);
        }

        return $this;
    }

    public function removeMapTile(MapTile $mapTile): self
    {
        if ($this->mapTiles->removeElement($mapTile)) {
            // set the owning side to null (unless already changed)
            if ($mapTile->getTile() === $this) {
                $mapTile->setTile(null);
            }
        }

        return $this;
    }
}
