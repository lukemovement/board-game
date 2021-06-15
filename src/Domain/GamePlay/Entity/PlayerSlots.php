<?php

namespace App\Domain\GamePlay\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GamePlay\Repository\PlayerSlotsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerSlotsRepository::class)
 */
#[ApiResource]
class PlayerSlots
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToOne(targetEntity=Player::class, mappedBy="playerSlots", cascade={"persist", "remove"})
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerItem::class)
     */
    private $head;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerItem::class)
     */
    private $leftHand;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerItem::class)
     */
    private $rightHand;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerItem::class)
     */
    private $chest;

    /**
     * @ORM\ManyToOne(targetEntity=PlayerItem::class)
     */
    private $legs;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(Player $player): self
    {
        // set the owning side of the relation if necessary
        if ($player->getPlayerSlots() !== $this) {
            $player->setPlayerSlots($this);
        }

        $this->player = $player;

        return $this;
    }

    public function getHead(): ?PlayerItem
    {
        return $this->head;
    }

    public function setHead(?PlayerItem $head): self
    {
        $this->head = $head;

        return $this;
    }

    public function getLeftHand(): ?PlayerItem
    {
        return $this->leftHand;
    }

    public function setLeftHand(?PlayerItem $leftHand): self
    {
        $this->leftHand = $leftHand;

        return $this;
    }

    public function getRightHand(): ?PlayerItem
    {
        return $this->rightHand;
    }

    public function setRightHand(?PlayerItem $rightHand): self
    {
        $this->rightHand = $rightHand;

        return $this;
    }

    public function getChest(): ?PlayerItem
    {
        return $this->chest;
    }

    public function setChest(?PlayerItem $chest): self
    {
        $this->chest = $chest;

        return $this;
    }

    public function getLegs(): ?PlayerItem
    {
        return $this->legs;
    }

    public function setLegs(?PlayerItem $legs): self
    {
        $this->legs = $legs;

        return $this;
    }
}
