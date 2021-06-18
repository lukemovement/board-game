<?php

namespace App\Domain\Jann\Environment\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Jann\Environment\Repository\TileZombieLinkRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TileZombieLinkRepository::class)
 */
#[ApiResource]
class TileZombieLink
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $linkCount;

    /**
     * @ORM\ManyToOne(targetEntity=ZombieState::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $zombieState;

    /**
     * @ORM\ManyToOne(targetEntity=TileState::class, inversedBy="zombieLinks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $tileState;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getZombieState(): ?ZombieState
    {
        return $this->zombieState;
    }

    public function setZombieState(?ZombieState $zombieState): self
    {
        $this->zombieState = $zombieState;

        return $this;
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
}
