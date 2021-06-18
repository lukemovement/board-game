<?php

namespace App\Domain\Jann\Agent\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Jann\Environment\Entity\PlayerState;
use App\Domain\Jann\Environment\Entity\TileState;
use App\Domain\Jann\Environment\Entity\ZombieState;
use App\Repository\Domain\Jann\Agent\Entity\DecisionRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=DecisionRepository::class)
 */
#[ApiResource]
class Decision
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
     * @ORM\ManyToOne(targetEntity=TileState::class, inversedBy="nextDecisions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $previousTileState;

    /**
     * @ORM\ManyToOne(targetEntity=TileState::class, inversedBy="previousDecisions")
     * @ORM\JoinColumn(nullable=false)
     */
    private $nextTileState;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerState::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $previousPlayerState;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerState::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $nextPlayerState;

    /**
     * @ORM\ManyToOne(targetEntity=ZombieState::class)
     */
    private $attackedZombieState;

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

    public function getPreviousTileState(): ?TileState
    {
        return $this->previousTileState;
    }

    public function setPreviousTileState(?TileState $previousTileState): self
    {
        $this->previousTileState = $previousTileState;

        return $this;
    }

    public function getNextTileState(): ?TileState
    {
        return $this->nextTileState;
    }

    public function setNextTileState(?TileState $nextTileState): self
    {
        $this->nextTileState = $nextTileState;

        return $this;
    }

    public function getPreviousPlayerState(): ?PlayerState
    {
        return $this->previousPlayerState;
    }

    public function setPreviousPlayerState(?PlayerState $previousPlayerState): self
    {
        $this->previousPlayerState = $previousPlayerState;

        return $this;
    }

    public function getNextPlayerState(): ?PlayerState
    {
        return $this->nextPlayerState;
    }

    public function setNextPlayerState(?PlayerState $nextPlayerState): self
    {
        $this->nextPlayerState = $nextPlayerState;

        return $this;
    }

    public function getAttackedZombieState(): ?ZombieState
    {
        return $this->attackedZombieState;
    }

    public function setAttackedZombieState(?ZombieState $attackedZombieState): self
    {
        $this->attackedZombieState = $attackedZombieState;

        return $this;
    }
}
