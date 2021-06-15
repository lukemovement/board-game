<?php

namespace App\Domain\GameData\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GamePlay\Entity\PlayerStat;
use App\Domain\GamePlay\Repository\PlayerStatConfigRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerStatConfigRepository::class)
 */
#[ApiResource]
class PlayerStatConfig
{
    const ATTACK_ID = 0;
    const HEALTH_ID = 1;
    const ENERGY_ID = 2;

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
     * @ORM\OneToMany(targetEntity=PlayerStat::class, mappedBy="playerStatConfig", orphanRemoval=true)
     */
    private $playerStats;

    /**
     * @ORM\OneToMany(targetEntity=PlayerStatModifier::class, mappedBy="playerStatConfig", orphanRemoval=true)
     */
    private $playerStatModifiers;

    /**
     * @ORM\Column(type="integer")
     */
    private $regenerationRate;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxLevel;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\Column(type="integer")
     */
    private $maxLevelIncreaseRate;

    /**
     * @ORM\Column(type="integer")
     */
    private $statTypeId;

    public function __construct(
        int $statTypeId,
        string $name,
        int $maxLevel,
        int $level,
        int $regenerationRate,
        int $maxLevelIncreaseRate
    )
    {
        $this->statTypeId = $statTypeId;
        $this->name = $name;
        $this->maxLevel = $maxLevel;
        $this->level = $level;
        $this->regenerationRate = $regenerationRate;
        $this->maxLevelIncreaseRate = $maxLevelIncreaseRate;

        $this->playerStats = new ArrayCollection();
        $this->playerStatModifiers = new ArrayCollection();
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

    /**
     * @return Collection|PlayerStat[]
     */
    public function getPlayerStats(): Collection
    {
        return $this->playerStats;
    }

    public function addPlayerStat(PlayerStat $playerStat): self
    {
        if (!$this->playerStats->contains($playerStat)) {
            $this->playerStats[] = $playerStat;
            $playerStat->setPlayerStatConfig($this);
        }

        return $this;
    }

    public function removePlayerStat(PlayerStat $playerStat): self
    {
        if ($this->playerStats->removeElement($playerStat)) {
            // set the owning side to null (unless already changed)
            if ($playerStat->getPlayerStatConfig() === $this) {
                $playerStat->setPlayerStatConfig(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|PlayerStatModifier[]
     */
    public function getPlayerStatModifiers(): Collection
    {
        return $this->playerStatModifiers;
    }

    public function addPlayerStatModifier(PlayerStatModifier $playerStatModifier): self
    {
        if (!$this->playerStatModifiers->contains($playerStatModifier)) {
            $this->playerStatModifiers[] = $playerStatModifier;
            $playerStatModifier->setPlayerStatConfig($this);
        }

        return $this;
    }

    public function removePlayerStatModifier(PlayerStatModifier $playerStatModifier): self
    {
        if ($this->playerStatModifiers->removeElement($playerStatModifier)) {
            // set the owning side to null (unless already changed)
            if ($playerStatModifier->getPlayerStatConfig() === $this) {
                $playerStatModifier->setPlayerStatConfig(null);
            }
        }

        return $this;
    }

    public function getRegenerationRate(): ?int
    {
        return $this->regenerationRate;
    }

    public function setRegenerationRate(int $regenerationRate): self
    {
        $this->regenerationRate = $regenerationRate;

        return $this;
    }

    public function getMaxLevel(): ?int
    {
        return $this->maxLevel;
    }

    public function setMaxLevel(int $maxLevel): self
    {
        $this->maxLevel = $maxLevel;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        return $this;
    }

    public function getMaxLevelIncreaseRate(): ?int
    {
        return $this->maxLevelIncreaseRate;
    }

    public function setMaxLevelIncreaseRate(int $maxLevelIncreaseRate): self
    {
        $this->maxLevelIncreaseRate = $maxLevelIncreaseRate;

        return $this;
    }

    public function getStatTypeId(): ?int
    {
        return $this->statTypeId;
    }

    public function setStatTypeId(int $statTypeId): self
    {
        $this->statTypeId = $statTypeId;

        return $this;
    }
}
