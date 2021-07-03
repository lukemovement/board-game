<?php

namespace App\Domain\Jann\Agent\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GamePlay\Entity\Game;
use App\Domain\Jann\Agent\Repository\AgentRepository;
use App\Domain\Jann\Behaviour\Entity\Behaviour;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=AgentRepository::class)
 */
#[ApiResource]
class Agent
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class)
     */
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity=Behaviour::class)
     */
    private $previousBehaviour;

    public function __construct(Game $game) {
        $this->game = $game;
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

    public function getPreviousBehaviour(): ?Behaviour
    {
        return $this->previousBehaviour;
    }

    public function setPreviousBehaviour(?Behaviour $previousBehaviour): self
    {
        $this->previousBehaviour = $previousBehaviour;

        return $this;
    }
}
