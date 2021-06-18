<?php

namespace App\Domain\GameData\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GameData\Entity\PlayerStatModifier;
use App\Domain\GamePlay\Entity\PlayerItem;
use App\Domain\GamePlay\Interface\AroundAwareInterface;
use App\Domain\GamePlay\Interface\RoundAwareInterface;
use App\Domain\GamePlay\Repository\ItemRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ItemRepository::class)
 */
#[ApiResource]
class Item implements RoundAwareInterface
{
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
     * @ORM\OneToMany(targetEntity=PlayerStatModifier::class, mappedBy="item", orphanRemoval=true)
     */
    private $modifiers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $minRound;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $maxRound;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $durability;

    /**
     * @ORM\OneToMany(targetEntity=PlayerItem::class, mappedBy="item", orphanRemoval=true)
     */
    private $playerItems;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fillsHeadSlot = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fillsChestSlot = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fillsLegsSlot = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fillsLeftHandSlot = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $fillsRightHandSlot = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $bothHandsSlot = false;

    /**
     * @ORM\Column(type="boolean")
     */
    private $useOnAttack;

    /**
     * @ORM\Column(type="boolean")
     */
    private $useOnDefence;

    /**
     * @ORM\Column(type="boolean")
     */
    private $useFromBackpack;


    public function __construct()
    {        
        $this->modifiers = new ArrayCollection();
        $this->playerItems = new ArrayCollection();
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
     * @return Collection|PlayerStatModifier[]
     */
    public function getModifiers(): Collection
    {
        return $this->modifiers;
    }

    public function addModifier(PlayerStatModifier $modifier): self
    {
        if (!$this->modifiers->contains($modifier)) {
            $this->modifiers[] = $modifier;
            $modifier->setItem($this);
        }

        return $this;
    }

    public function removeModifier(PlayerStatModifier $modifier): self
    {
        if ($this->modifiers->removeElement($modifier)) {
            // set the owning side to null (unless already changed)
            if ($modifier->getItem() === $this) {
                $modifier->setItem(null);
            }
        }

        return $this;
    }

    public function getMinRound(): ?int
    {
        return $this->minRound;
    }

    public function setMinRound(?int $minRound): self
    {
        $this->minRound = $minRound;

        return $this;
    }

    public function getMaxRound(): ?int
    {
        return $this->maxRound;
    }

    public function setMaxRound(?int $maxRound): self
    {
        $this->maxRound = $maxRound;

        return $this;
    }

    public function getDurability(): ?int
    {
        return $this->durability;
    }

    public function setDurability(?int $durability): self
    {
        $this->durability = $durability;

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
            $playerItem->setItem($this);
        }

        return $this;
    }

    public function removePlayerItem(PlayerItem $playerItem): self
    {
        if ($this->playerItems->removeElement($playerItem)) {
            // set the owning side to null (unless already changed)
            if ($playerItem->getItem() === $this) {
                $playerItem->setItem(null);
            }
        }

        return $this;
    }

    public function getFillsHeadSlot(): bool
    {
        return $this->fillsHeadSlot;
    }

    public function setFillsHeadSlot(bool $fillsHeadSlot): self
    {
        $this->fillsHeadSlot = $fillsHeadSlot;

        return $this;
    }

    public function getFillsChestSlot(): bool
    {
        return $this->fillsChestSlot;
    }

    public function setFillsChestSlot(bool $fillsChestSlot): self
    {
        $this->fillsChestSlot = $fillsChestSlot;

        return $this;
    }

    public function getFillsLegsSlot(): bool
    {
        return $this->fillsLegsSlot;
    }

    public function setFillsLegsSlot(bool $fillsLegsSlot): self
    {
        $this->fillsLegsSlot = $fillsLegsSlot;

        return $this;
    }

    public function getFillsLeftHandSlot(): bool
    {
        return $this->fillsLeftHandSlot;
    }

    public function setFillsLeftHandSlot(bool $fillsLeftHandSlot): self
    {
        $this->fillsLeftHandSlot = $fillsLeftHandSlot;

        return $this;
    }

    public function getFillsRightHandSlot(): bool
    {
        return $this->fillsRightHandSlot;
    }

    public function setFillsRightHandSlot(bool $fillsRightHandSlot): self
    {
        $this->fillsRightHandSlot = $fillsRightHandSlot;

        return $this;
    }

    public function getFillsBothHandsSlot(): bool
    {
        return $this->bothHandsSlot;
    }

    public function setFillsBothHandsSlot(bool $bothHandsSlot): self
    {
        $this->bothHandsSlot = $bothHandsSlot;

        return $this;
    }

    public function getUseOnAttack(): ?bool
    {
        return $this->useOnAttack;
    }

    public function setUseOnAttack(bool $useOnAttack): self
    {
        $this->useOnAttack = $useOnAttack;

        return $this;
    }

    public function getUseOnDefence(): ?bool
    {
        return $this->useOnDefence;
    }

    public function setUseOnDefence(bool $useOnDefence): self
    {
        $this->useOnDefence = $useOnDefence;

        return $this;
    }

    public function getUseFromBackpack(): ?bool
    {
        return $this->useFromBackpack;
    }

    public function setUseFromBackpack(bool $useFromBackpack): self
    {
        $this->useFromBackpack = $useFromBackpack;

        return $this;
    }

    public function isItemEquippable(): bool
    {
        $rightHand = $this->getFillsRightHandSlot();
        $leftHand = $this->getFillsLeftHandSlot();
        $chest = $this->getFillsChestSlot();
        $head = $this->getFillsHeadSlot();
        $legs = $this->getFillsLegsSlot();

        return in_array(true, [ 
            $rightHand,
            $leftHand,
            $chest,
            $head,
            $legs
        ]);
    }
}
