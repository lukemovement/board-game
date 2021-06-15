<?php

declare(strict_types=1);

namespace App\Domain\GamePlay\Interface;

interface RoundAwareInterface {
    public function getMaxRound(): ?int;
    public function setMaxRound(?int $maxRound);
    public function getMinRound(): ?int;
    public function setMinRound(?int $minRound);
}