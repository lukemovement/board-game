<?php

namespace App\Domain\GamePlay\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GameData\Entity\Item;
use App\Domain\GameData\Entity\PlayerStatModifier;
use App\Domain\GamePlay\Repository\PlayerItemRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PlayerItemRepository::class)
 */
#[ApiResource]
class PlayerItem
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="playerItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity=Item::class, inversedBy="playerItems")
     * @ORM\JoinColumn(nullable=false)
     */
    private $item;

    /**
     * @ORM\Column(type="integer")
     */
    private $remainingUses;

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

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function setItem(?Item $item): self
    {
        $this->item = $item;

        return $this;
    }

    public function getRemainingUses(): ?int
    {
        return $this->remainingUses;
    }

    public function setRemainingUses(int $remainingUses): self
    {
        $this->remainingUses = $remainingUses;

        return $this;
    }

    public function isItemEquipped(): bool
    {
        $legs = $this->getPlayer()->getPlayerSlots()->getLegs();
        $head = $this->getPlayer()->getPlayerSlots()->getHead();
        $chest = $this->getPlayer()->getPlayerSlots()->getChest();
        $leftHand = $this->getPlayer()->getPlayerSlots()->getLeftHand();
        $rightHand = $this->getPlayer()->getPlayerSlots()->getRightHand();

        return in_array(true, [ 
            null !== $legs ? $legs->getId() === $this->id : false, 
            null !== $head ? $head->getId() === $this->id : false, 
            null !== $chest ? $chest->getId() === $this->id : false, 
            null !== $leftHand ? $leftHand->getId() === $this->id : false, 
            null !== $rightHand ? $rightHand->getId() === $this->id : false, 
        ]);
    }

    public function getComputedModifier(PlayerStat $playerStat): int
    {
        if ($this->item->isItemEquippable() && !$this->isItemEquipped()) {
            return 0;
        }

        $modifierValue = 0;

        $this->item->getModifiers()->forAll(function(PlayerStatModifier $modifier) use ($playerStat, &$modifierValue)
        {
            if (
                $modifier->getPlayerStatConfig()->getId() !==
                $playerStat->getPlayerStatConfig()->getId()
            ) {
                return true;
            }

            $modifierValue = $modifierValue + $modifier->getValue();

            return true;
        });

        return $modifierValue;
    }
}
