<?php

namespace App\Domain\Profile\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GamePlay\Entity\Player;
use App\Domain\Profile\Repository\ProfileRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ProfileRepository::class)
 */
#[ApiResource]
class Profile
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $googleAccountId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nickname;

    /**
     * @ORM\OneToMany(targetEntity=Player::class, mappedBy="profile", orphanRemoval=true)
     */
    private $players;

    public function __construct(
        string $nickname
    )
    {
        $this->nickname = $nickname;
        
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoogleAccountId(): ?int
    {
        return $this->googleAccountId;
    }

    public function setGoogleAccountId(int $googleAccountId): self
    {
        $this->googleAccountId = $googleAccountId;

        return $this;
    }

    public function getNickname(): ?string
    {
        return $this->nickname;
    }

    public function setNickname(string $nickname): self
    {
        $this->nickname = $nickname;

        return $this;
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
            $player->setProfile($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getProfile() === $this) {
                $player->setProfile(null);
            }
        }

        return $this;
    }
}
