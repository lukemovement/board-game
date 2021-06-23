<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Dto;

use App\Domain\Jann\Agent\Entity\Decision;

class DecisionEvaluationDto {

    public Decision $decision;

    public float $chance;

    public float $reward;
}