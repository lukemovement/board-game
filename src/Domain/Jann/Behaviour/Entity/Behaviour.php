<?php

namespace App\Domain\Jann\Behaviour\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Jann\Environment\Entity\PlayerState;
use App\Domain\Jann\Environment\Entity\TileState;
use App\Domain\Jann\Environment\Entity\ZombieState;
use App\Domain\Jann\Behaviour\Repository\BehaviourRepository;
use App\Domain\Jann\NeuralNetworkConfig;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=BehaviourRepository::class)
 */
#[ApiResource]
class Behaviour
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
     * @ORM\ManyToOne(targetEntity=Behaviour::class)
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
        ?TileState $currentTileState,
        ?TileState $movedToTileState,
        ?PlayerState $previousPlayerState,
        ?PlayerState $nextPlayerState,
        ?ZombieState $attackedZombieStateBefore,
        ?ZombieState $attackedZombieStateAfter,
    ) {
        $this->currentTileState = $currentTileState;
        $this->movedToTileState = $movedToTileState;
        $this->previousPlayerState = $previousPlayerState;
        $this->nextPlayerState = $nextPlayerState;
        $this->attackedZombieStateBefore = $attackedZombieStateBefore;
        $this->attackedZombieStateAfter = $attackedZombieStateAfter;
    }

    public function isTypeMove(): bool
    {
        return null !== $this->movedToTileState;
    }

    public function isTypeAttack(): bool
    {
        return false === in_array(false, [
            $this->attackedZombieStateBefore,
            $this->attackedZombieStateAfter,
        ]);
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

    public function getBehaviourReward(): int|null
    {
        if (null === $this->getNextPlayerState()) {
            return null;
        }

        $healthReward = (
            $this->getNextPlayerState()->getMaxHealth() /
            $this->getPreviousPlayerState()->getHealth()
        ) * NeuralNetworkConfig::BEHAVIOUR_HEALTH_PRIORITY;

        $killReward = 0;
        if (
            null !== $this->getAttackedZombieStateBefore() &&
            null !== $this->getAttackedZombieStateAfter() &&
            $this->getAttackedZombieStateBefore()->getId() === $this->getAttackedZombieStateAfter()->getId()
        ) {
            $killReward = (
                $this->getAttackedZombieStateBefore()->getCount() -
                $this->getAttackedZombieStateAfter()->getCount()
            ) * NeuralNetworkConfig::BEHAVIOUR_KILL_PRIORITY;
        } 

        $damageReward = 0;
        if (
            null !== $this->getAttackedZombieStateBefore() &&
            null !== $this->getAttackedZombieStateAfter() &&
            $this->getAttackedZombieStateBefore()->getId() !== $this->getAttackedZombieStateAfter()->getId()
        ) {
            $damageDiff = (
                $this->getAttackedZombieStateBefore()->getHealth() -
                $this->getAttackedZombieStateAfter()->getHealth()
            );

            $damageReward = (                
                $this->getAttackedZombieStateAfter()->getZombieType()->getHealth() /
                $damageDiff
            ) * NeuralNetworkConfig::BEHAVIOUR_DAMAGE_PRIORITY;
        } 

        return $healthReward + $damageReward + $killReward;
    }

    public function getLevelTileState(): ?self
    {
        return $this->currentTileState;
    }

    public function setLevelTileState(?self $currentTileState): self
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

    public function environmentMatches(Behaviour $behaviour): bool
    {
        if (false === in_array(true, [
            $behaviour->isTypeAttack() === $this->isTypeAttack(),
            $behaviour->isTypeMove() === $this->isTypeMove(),
        ])) {
            return false;
        }

        if (
            $behaviour->isTypeAttack() &&
            (
                $behaviour->getPreviousPlayerState()->getId() !== $this->getPreviousPlayerState()->getId() ||
                $behaviour->getLevelTileState()->getId() !== $this->getLevelTileState()->getId()
            )
        ) {
            return false;
        }

        if (
            $behaviour->isTypeMove() &&
            (
                $behaviour->getPreviousPlayerState()->getId() !== $this->getPreviousPlayerState()->getId() &&
                $behaviour->getLevelTileState()->getId() !== $this->getLevelTileState()->getId() ||
                $behaviour->getMovedToTileState()->getId() !== $this->getMovedToTileState()->getId()
            )
        ) {
            return false;
        }

        return true;
    }
}
