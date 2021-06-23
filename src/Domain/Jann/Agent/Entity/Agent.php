<?php

namespace App\Domain\Jann\Agent\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use App\Domain\GamePlay\Entity\Game;
use App\Repository\Domain\Jann\Agent\Entity\AgentRepository;
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
     * @ORM\ManyToOne(targetEntity=Decision::class)
     */
    private $previousDecision;

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

    public function getPreviousDecision(): ?Decision
    {
        return $this->previousDecision;
    }

    public function setPreviousDecision(?Decision $previousDecision): self
    {
        $this->previousDecision = $previousDecision;

        return $this;
    }
}
