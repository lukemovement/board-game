<?php

namespace App\Domain\Jann\Agent\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\Jann\Agent\Repository\TileTrackingRepository;
use App\Domain\Jann\Environment\Entity\TileState;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TileStateTrackingRepository::class)
 */
#[ApiResource]
class TileStateTracking
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
     * @ORM\ManyToOne(targetEntity=MapTile::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $mapTile;

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

    public function getTile(): ?MapTile
    {
        return $this->mapTile;
    }

    public function setTile(?MapTile $mapTile): self
    {
        $this->mapTile = $mapTile;

        return $this;
    }
}
