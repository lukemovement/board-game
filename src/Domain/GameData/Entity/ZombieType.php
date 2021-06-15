<?php

namespace App\Domain\GameData\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GamePlay\Entity\Zombie;
use App\Domain\GameData\Repository\ZombieTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ZombieTypeRepository::class)
 */
#[ApiResource]
class ZombieType
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
     * @ORM\Column(type="string", length=255)
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     */
    private $attack;

    /**
     * @ORM\Column(type="integer")
     */
    private $health;

    /**
     * @ORM\Column(type="integer")
     */
    private $speed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $vision;

    /**
     * @ORM\OneToMany(targetEntity=Zombie::class, mappedBy="zombieType", orphanRemoval=true)
     */
    private $zombies;

    public function __construct()
    {
        $this->zombies = new ArrayCollection();
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

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getAttack(): ?int
    {
        return $this->attack;
    }

    public function setAttack(int $attack): self
    {
        $this->attack = $attack;

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

    public function getSpeed(): ?int
    {
        return $this->speed;
    }

    public function setSpeed(int $speed): self
    {
        $this->speed = $speed;

        return $this;
    }

    public function getVision(): ?int
    {
        return $this->vision;
    }

    public function setVision(?int $vision): self
    {
        $this->vision = $vision;

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
            $zombie->setZombieType($this);
        }

        return $this;
    }

    public function removeZombie(Zombie $zombie): self
    {
        if ($this->zombies->removeElement($zombie)) {
            // set the owning side to null (unless already changed)
            if ($zombie->getZombieType() === $this) {
                $zombie->setZombieType(null);
            }
        }

        return $this;
    }
}
