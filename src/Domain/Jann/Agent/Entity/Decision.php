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
     * @ORM\ManyToOne(targetEntity=Decision::class)
     * @ORM\JoinColumn(nullable=false)
     */
    private $currentTileState;

    /**
     * @ORM\ManyToOne(targetEntity=TileState::class)
     */
    private $movedToTileState;

    /**
     * @ORM\ManyToOne(targetEntity=ZombieState::class)
     */
    private $attackedZombieStateBefore;

    /**
     * @ORM\ManyToOne(targetEntity=ZombieState::class)
     */
    private $attackedZombieStateAfter;

    public function __construct(
        ?TileState $previousTileState,
        ?TileState $nextTileState,
        ?PlayerState $previousPlayerState,
        ?PlayerState $nextPlayerState,
        ?ZombieState $attackedZombieStateBefore,
        ?ZombieState $attackedZombieStateAfter,
    ) {
        $this->previousTileState = $previousTileState;
        $this->nextTileState = $nextTileState;
        $this->previousPlayerState = $previousPlayerState;
        $this->nextPlayerState = $nextPlayerState;
        $this->attackedZombieStateBefore = $attackedZombieStateBefore;
        $this->attackedZombieStateAfter = $attackedZombieStateAfter;
    }

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

    public function getDecisionValue(): int|null
    {
        if (null === $this->getNextPlayerState()) {
            return null;
        }

        $healthDiff = 
            $this->getNextPlayerState()->getHealth() -
            $this->getPreviousPlayerState()->getHealth();

        
        $zombieDiff = 
            null !== $this->getAttackedZombieStateBefore() &&
            null !== $this->getAttackedZombieStateAfter() ? 
            $this->getAttackedZombieStateBefore()->getCount() -
            $this->getAttackedZombieStateAfter()->getCount() : 0;

        return $healthDiff + $zombieDiff;
    }

    public function getCurrentTileState(): ?self
    {
        return $this->currentTileState;
    }

    public function setCurrentTileState(?self $currentTileState): self
    {
        $this->currentTileState = $currentTileState;

        return $this;
    }

    public function getMovedToTileState(): ?TileState
    {
        return $this->movedToTileState;
    }

    public function setMovedToTileState(?TileState $movedToTileState): self
    {
        $this->movedToTileState = $movedToTileState;

        return $this;
    }

    public function getAttackedZombieStateBefore(): ?ZombieState
    {
        return $this->attackedZombieStateBefore;
    }

    public function setAttackedZombieStateBefore(?ZombieState $attackedZombieStateBefore): self
    {
        $this->attackedZombieStateBefore = $attackedZombieStateBefore;

        return $this;
    }

    public function getAttackedZombieStateAfter(): ?ZombieState
    {
        return $this->attackedZombieStateAfter;
    }

    public function setAttackedZombieStateAfter(?ZombieState $attackedZombieStateAfter): self
    {
        $this->attackedZombieStateAfter = $attackedZombieStateAfter;

        return $this;
    }
}
