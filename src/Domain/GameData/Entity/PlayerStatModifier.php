<?php

namespace App\Domain\GameData\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GameData\Entity\Item;
use App\Domain\GamePlay\Repository\PlayerStatModifierRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerStatModifierRepository::class)
 */
#[ApiResource]
class PlayerStatModifier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", length=255)
     */
    private $value;

    /**
     * @ORM\ManyToOne(targetEntity=Item::class, inversedBy="modifiers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerStatConfig::class, inversedBy="playerStatModifiers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $playerStatConfig;

    public function __construct(
        PlayerStatConfig $playerStatConfig,
        int $value
    ) {
        $this->playerStatConfig = $playerStatConfig;
        $this->value = $value;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): ?int
    {
        return $this->value;
    }

    public function setValue(int $value): self
    {
        $this->value = $value;

        return $this;
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

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
}
