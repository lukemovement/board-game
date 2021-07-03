<?php

namespace App\Domain\GamePlay\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\Common\Type\Position;
use App\Domain\GameData\Entity\MapTile;
use App\Domain\GameData\Entity\PlayerStatConfig;
use App\Domain\GameData\Entity\Tile;
use App\Domain\GameData\Entity\ZombieType;
use App\Domain\GamePlay\Interface\MovableInterface;
use App\Domain\GamePlay\Repository\ZombieRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ZombieRepository::class)
 */
#[ApiResource]
class Zombie implements MovableInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="zombies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $game;

    /**
     * @var Position
     * @ORM\Column(type="position")
     */
    private $position;

    /**
     * @ORM\ManyToOne(targetEntity=ZombieType::class, inversedBy="zombies")
     * @ORM\JoinColumn(nullable=false)
     */
    private $zombieType;

    /**
     * @ORM\Column(type="integer")
     */
    private $health;

    public function __construct(
        Game $game,
        Position $position,
        ZombieType $zombieType
    ) {
        $this->game = $game;
        $this->position = $position;
        $this->zombieType = $zombieType;
        $this->health = $zombieType->getHealth();
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

    public function getPosition(): Position
    {
        return $this->position;
    }

    public function setPosition(Position $position): self
    {
        if (
            null !== $this->position &&
            $this->position->matches($position)
        ) {
            return false;
        }

        $this->position = $position;

        return $this;
    }

    public function getZombieType(): ?ZombieType
    {
        return $this->zombieType;
    }

    public function setZombieType(?ZombieType $zombieType): self
    {
        $this->zombieType = $zombieType;

        return $this;
    }

    public function getHealth(): ?int
    {
        return $this->health;
    }

    public function setHealth(int $health): self
    {
        $this->health = $health;

        return $this;
    }

    public function getMapTile(): MapTile
    {
        return $this->game->getMap()->getMapTile($this->position);
    }

    public function attackPlayer(Player $player): void
    {
        /** @var PlayerStat $playerHealthStat */
        $playerHealthStat = $player->getPlayerStats()->filter(
            fn(PlayerStat $playerStat) => $playerStat
                ->getPlayerStatConfig()->getStatTypeId() === PlayerStatConfig::HEALTH_ID
        )->first();

        $playerHealthStat->setLevel($playerHealthStat->getLevel() - $this->getZombieType()->getAttack());
    }
}
