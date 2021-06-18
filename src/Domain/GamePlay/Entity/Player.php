<?php

namespace App\Domain\GamePlay\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GamePlay\Interface\MovableInterface;
use App\Domain\GamePlay\Repository\PlayerRepository;
use App\Domain\Profile\Entity\Profile;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 */
#[ApiResource]
class Player implements MovableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @ORM\OneToMany(targetEntity=PlayerStat::class, mappedBy="player", orphanRemoval=true)
     */
    private $playerStats;

    /**
     * @ORM\ManyToOne(targetEntity=Profile::class, inversedBy="players")
     * @ORM\JoinColumn(nullable=false)
     */
    private $profile;

    /**
     * @ORM\Column(type="position")
     */
    private $position;

    /**
     * @ORM\Column(type="boolean")
     */
    private $takenTurn = false;

    /**
     * @ORM\OneToMany(targetEntity=PlayerItem::class, mappedBy="player", orphanRemoval=true)
     */
    private $playerItems;

    /**
     * @ORM\OneToOne(targetEntity=PlayerSlots::class, inversedBy="player", cascade={"persist", "remove"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $playerSlots;

    public function __construct(
        Profile $profile
    )
    {
        $this->profile = $profile;
        $this->playerStats = new ArrayCollection();
        $this->position = new Position([0,0]);
        $this->playerItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

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
            $playerStat->setPlayer($this);
        }

        return $this;
    }

    public function removePlayerStat(PlayerStat $playerStat): self
    {
        if ($this->playerStats->removeElement($playerStat)) {
            // set the owning side to null (unless already changed)
            if ($playerStat->getPlayer() === $this) {
                $playerStat->setPlayer(null);
            }
        }

        return $this;
    }

    public function getProfile(): ?Profile
    {
        return $this->profile;
    }

    public function setProfile(?Profile $profile): self
    {
        $this->profile = $profile;

        return $this;
    }

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): self
    {
        $this->position = $position;

        return $this;
    }

    public function getTakenTurn(): ?bool
    {
        return $this->takenTurn;
    }

    public function setTakenTurn(bool $takenTurn): self
    {
        $this->takenTurn = $takenTurn;

        return $this;
    }

    /**
     * @return Collection|PlayerItem[]
     */
    public function getPlayerItems(): Collection
    {
        return $this->playerItems;
    }

    public function addPlayerItem(PlayerItem $playerItem): self
    {
        if (!$this->playerItems->contains($playerItem)) {
            $this->playerItems[] = $playerItem;
            $playerItem->setPlayer($this);
        }

        return $this;
    }

    public function removePlayerItem(PlayerItem $playerItem): self
    {
        if ($this->playerItems->removeElement($playerItem)) {
            // set the owning side to null (unless already changed)
            if ($playerItem->getPlayer() === $this) {
                $playerItem->setPlayer(null);
            }
        }

        return $this;
    }

    public function getPlayerSlots(): ?PlayerSlots
    {
        return $this->playerSlots;
    }

    public function setPlayerSlots(PlayerSlots $playerSlots): self
    {
        $this->playerSlots = $playerSlots;

        return $this;
    }

    public function isAlive(): bool
    {
        /** @var PlayerStat $healthPlayerStat */
        $healthPlayerStat = $this->playerStats->filter(
            fn(PlayerStat $ps) => $ps->getPlayerStatConfig()
                ->getStatTypeId() === PlayerStatConfig::HEALTH_ID
        )->first();

        return $healthPlayerStat->getLevel() > 0;
    }

    public function getPlayerStat(int $statTypeId): ?PlayerStat
    {
        return $this->getPlayerStats()->filter(
            fn(PlayerStat $playerStat) => $playerStat->getPlayerStatConfig()
                ->getStatTypeId() === $statTypeId
        )->first();
    }
}
