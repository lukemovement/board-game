<?php

namespace App\Domain\GamePlay\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Repository\PlayerStatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerStatRepository::class)
 */
#[ApiResource]
class PlayerStat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="playerStats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\Column(type="integer")
     */
    private $level;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerStatConfig::class, inversedBy="playerStats")
     * @ORM\JoinColumn(nullable=false)
     */
    private $playerStatConfig;

    public function __construct(
        PlayerStatConfig $playerStatConfig
    )
    {
        $this->playerStatConfig = $playerStatConfig;
        $this->level = $playerStatConfig->getLevel();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getLevel(): ?int
    {
        return $this->level;
    }

    public function setLevel(int $level): self
    {
        $this->level = $level;

        if ($this->level > $this->getPlayerStatConfig()->getMaxLevel()) {
            $this->level = $this->getPlayerStatConfig()->getMaxLevel();
        }

        return $this;
    }

    public function getPlayerStatConfig(): ?PlayerStatConfig
    {
        return $this->playerStatConfig;
    }

    public function setPlayerStatConfig(?PlayerStatConfig $playerStatConfig): self
    {
        $this->playerStatConfig = $playerStatConfig;

        return $this;
    }

    public function getComputedLevel(): int
    {
        $level = $this->getLevel();

        $this->player->getPlayerItems()->forAll(function(int $i, PlayerItem $playerItem) use (&$level)
        {
            $level = $level + $playerItem->getComputedModifier($this);
        });

        return $level;
    }

    public function decrease(int $val)
    {
        $this->level = $this->level - $val;

        if ($this->level < 0) {
            $this->level = 0;
        }

        return $this;
    }
}
