<?php

namespace App\Domain\GameData\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Common\Type\Position;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\GameData\Repository\MapRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

/**
 * @ORM\Entity(repositoryClass=MapRepository::class)
 */
#[ApiResource]
class Map
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="integer", name="map_columns")
     */
    private $columns;

    /**
     * @ORM\Column(type="integer", name="map_rows")
     */
    private $rows;

    /**
     * @ORM\OneToMany(targetEntity=MapTile::class, mappedBy="map", cascade={"persist", "remove"})
     */
    private $mapTiles;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="map", orphanRemoval=true)
     */
    private $games;

    /**
     * @ORM\Column(type="integer")
     */
    private $zombievisibility;

    /**
     * @ORM\Column(type="integer")
     */
    private $itemLimit;

    /**
     * Use MapGeneratorService to create a map
     *
     * @param string $name
     * @param integer $rows
     * @param integer $columns
     * @param integer $zombievisibility
     * @param integer $itemLimit
     */
    public function __construct(
        string $name,
        int $rows,
        int $columns,
        int $zombievisibility,
        int $itemLimit
    )
    {
        $this->name = $name;
        $this->rows = $rows;
        $this->columns = $columns;
        $this->zombievisibility = $zombievisibility;
        $this->itemLimit = $itemLimit;
        
        $this->mapTiles = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getColumns(): ?int
    {
        return $this->columns;
    }

    public function setColumns(int $columns): self
    {
        $this->columns = $columns;

        return $this;
    }

    public function getRows(): ?int
    {
        return $this->rows;
    }

    public function setRows(int $rows): self
    {
        $this->rows = $rows;

        return $this;
    }

    /**
     * @return Collection|MapTile[]
     */
    public function getMapTiles(): Collection
    {
        return $this->mapTiles;
    }

    /**
     * @param Collection<int, MapTile> $mapTiles
     * @return self
     */
    public function setMapTiles(Collection $mapTiles): self {
        $this->mapTiles->forAll(function(int $i, MapTile $mapTile) {
            $this->removeMapTile($mapTile);
            return true;
        });

        $mapTiles->forAll(function(int $i, MapTile $mapTile) {
            $this->addMapTile($mapTile);
            return true;
        });

        return $this;
    }

    private function addMapTile(MapTile $mapTile): self
    {
        if (!$this->mapTiles->contains($mapTile)) {
            $this->mapTiles[] = $mapTile;
            $mapTile->setMap($this);
        }

        return $this;
    }

    private function removeMapTile(MapTile $mapTile): self
    {
        if ($this->mapTiles->removeElement($mapTile)) {
            // set the owning side to null (unless already changed)
            if ($mapTile->getMap() === $this) {
                $mapTile->setMap(null);
            }
        }

        return $this;
    }

    public function getMapTile(Position $position): MapTile
    {
        return $this->getMapTiles()->filter(fn(MapTile $mapTile) => $mapTile->getPosition()->matches($position))->first();
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setMap($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getMap() === $this) {
                $game->setMap(null);
            }
        }

        return $this;
    }

    public function getZombieVisibility(): ?int
    {
        return $this->zombievisibility;
    }

    public function setZombieVisibility(int $zombievisibility): self
    {
        $this->zombievisibility = $zombievisibility;

        return $this;
    }

    public function getItemLimit(): ?int
    {
        return $this->itemLimit;
    }

    public function setItemLimit(int $itemLimit): self
    {
        $this->itemLimit = $itemLimit;

        return $this;
    }
}
