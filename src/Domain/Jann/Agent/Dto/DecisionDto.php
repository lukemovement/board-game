<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Dto;

use App\Domain\Jann\Behaviour\Entity\Behaviour;

class DecisionDto {

    public Behaviour $behaviour;

    public float $chance;

    public float $reward;
}