<?php

namespace App\Domain\GamePlay\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\Map;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GamePlay\Dto\PathFinderNodeDto;
use App\Domain\GamePlay\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
#[ApiResource]
class Game
{
    public const GAME_DIFFICULTY_EASY = 0.20;
    public const GAME_DIFFICULTY_MEDIUM = 0.40;
    public const GAME_DIFFICULTY_HARD = 0.60;
    public const GAME_DIFFICULTY_VERY_HARD = 0.80;
    public const GAME_DIFFICULTY_INSANE = 1;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Player::class, mappedBy="game", cascade={ "persist", "remove" })
     */
    private $players;

    /**
     * @ORM\Column(type="integer")
     */
    private $round = 0;

    /**
     * @ORM\ManyToOne(targetEntity=Map::class, inversedBy="games")
     * @ORM\JoinColumn(nullable=false)
     */
    private $map;

    /**
     * @ORM\OneToMany(targetEntity=Zombie::class, mappedBy="game", cascade={ "persist", "remove" })
     */
    private $zombies;

    /**
     * @ORM\Column(type="integer")
     */
    private $dayLength = 7;

    /**
     * @ORM\Column(type="integer")
     */
    private $nightLength = 3;

    /**
     * @ORM\OneToMany(targetEntity=SearchableInteraction::class, mappedBy="game", orphanRemoval=true)
     */
    private $searchableInteractions;

    /**
     * @ORM\Column(type="float")
     */
    private $difficulty = 0.20;

    public function __construct(
        Map $map
    )
    {
        $this->map = $map;

        $this->players = new ArrayCollection();
        $this->zombies = new ArrayCollection();
        $this->searchableInteractions = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setGame($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGame() === $this) {
                $player->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Tile[]
     */
    public function getTiles(): Collection
    {
        return $this->tiles;
    }

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function increaseRound(): self
    {
        $this->round++;

        return $this;
    }

    public function getMap(): ?Map
    {
        return $this->map;
    }

    public function setMap(?Map $map): self
    {
        $this->map = $map;

        return $this;
    }

    /**
     * @return Collection|Zombie[]
     */
    public function getZombies(): Collection
    {
        return $this->zombies;
    }

    public function addZombie(Zombie $zombie): self
    {
        if (!$this->zombies->contains($zombie)) {
            $this->zombies[] = $zombie;
            $zombie->setGame($this);
        }

        return $this;
    }

    public function removeZombie(Zombie $zombie): self
    {
        if ($this->zombies->removeElement($zombie)) {
            // set the owning side to null (unless already changed)
            if ($zombie->getGame() === $this) {
                $zombie->setGame(null);
            }
        }

        return $this;
    }

    /**
     * @return ArrayCollection|Player[]
     */
    public function getPlayersAtPosition(Position $position): ArrayCollection 
    {
        return $this->players->filter(fn(Player $player) => $player->getPosition()->matches($position));
    }

    /**
     * @return ArrayCollection|SearchableInteraction[]
     */
    public function getSearchableInteractionsPosition(Position $position): ArrayCollection 
    {
        return $this->searchableInteractions->filter(fn(SearchableInteraction $player) => $player->getPosition()->matches($position));
    }

    /**
     * @return ArrayCollection|Zombie[]
     */
    public function getZombiesAtPosition(Position $position): ArrayCollection 
    {
        return $this->zombies->filter(fn(Zombie $player) => $player->getPosition()->matches($position));
    }

    public function getDayLength(): ?int
    {
        return $this->dayLength;
    }

    public function setDayLength(int $dayLength): self
    {
        $this->dayLength = $dayLength;

        return $this;
    }

    public function getNightLength(): ?int
    {
        return $this->nightLength;
    }

    public function setNightLength(int $nightLength): self
    {
        $this->nightLength = $nightLength;

        return $this;
    }

    /**
     * @return Collection|SearchableInteraction[]
     */
    public function getSearchableInteractions(): Collection
    {
        return $this->searchableInteractions;
    }

    public function addSearchableInteraction(SearchableInteraction $searchableInteraction): self
    {
        if (!$this->searchableInteractions->contains($searchableInteraction)) {
            $this->searchableInteractions[] = $searchableInteraction;
            $searchableInteraction->setGame($this);
        }

        return $this;
    }

    public function removeSearchableInteraction(SearchableInteraction $searchableInteraction): self
    {
        if ($this->searchableInteractions->removeElement($searchableInteraction)) {
            // set the owning side to null (unless already changed)
            if ($searchableInteraction->getGame() === $this) {
                $searchableInteraction->setGame(null);
            }
        }

        return $this;
    }

    public function isDay(): bool
    {
        $round = 0;
        while(true) {
            if ($round <= $this->round && $this->round < ($round + $this->dayLength)) {
                return true;
            }

            $round = $round + $this->dayLength;


            if ($round <= $this->round && $this->round < ($round + $this->nightLength)) {
                return false;
            }

            $round = $round + $this->nightLength;
        }
    }

    public function getZombieHealthModifier(): int
    {
        return ceil(
            $this->round *
            $this->getDifficulty()
        );
    }

    public function getMaxZombies(): int
    {
        return ceil(
            (
                $this->getDifficulty() *
                $this->getRound() *
                $this->players->count()
            ) * $this->isDay() ? 2 : 5
        );
    }

    public function getDifficulty(): ?float
    {
        return $this->difficulty;
    }

    public function setDifficulty(float $difficulty): self
    {
        $this->difficulty = $difficulty;

        return $this;
    }

    /** 
     * @return Collection|Player[]
     */
    public function getLivingPlayers(): Collection
    {
        return $this->getPlayers()->filter(fn(Player $player) => $player->isAlive());
    }

    /**
     * @return ArrayCollection|PathFinderNodeDto
     */
    public function getAvailableRoutes(
        MapTile $mapTile,
        int $maxDepth,
        ArrayCollection $route = null,
        ArrayCollection $availableMoves = null,
        $currentDepth = 0,
    ): ArrayCollection
    {        
        if ($maxDepth === $currentDepth) {
            return $availableMoves;
        }

        $currentDepth++;

        if (null === $availableMoves) {
            $availableMoves = new ArrayCollection();
        }

        if (null === $route) {
            $route = new ArrayCollection();
        }

        $mapTile->getAdjacentTiles()->forAll(
            function(int $i, MapTile $mapTile) use (
                $route,
                $currentDepth,
                $availableMoves,
                $maxDepth
            ) {
                if ($route->filter(
                    fn(PathFinderNodeDto $pathFinderNodeDto) => 
                        $pathFinderNodeDto->destination->getPosition()->matches(
                            $mapTile->getPosition()
                        )
                    )->count() > 0
                ) {
                    return true;
                }

                $node = new PathFinderNodeDto();

                $node->destination = $mapTile;
                $route->add($mapTile);
                $node->route = $route;

                $availableMoves->add($node);

                $newRoute = clone $route;
                $newRoute->add($node);

                $this->getAvailableRoutes($mapTile, $maxDepth, $newRoute, $availableMoves, $currentDepth);

                return true;
            } 
        );

        return $availableMoves;
    }
}
