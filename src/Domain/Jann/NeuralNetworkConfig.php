<?php

declare(strict_types=1);

namespace App\Domain\Jann;

class NeuralNetworkConfig {
    public const ENVIRONMENT_SEARCH_DEPTH = 4;

    public const BEHAVIOUR_HEALTH_PRIORITY = 100;
    public const BEHAVIOUR_KILL_PRIORITY = 50;
    public const BEHAVIOUR_DAMAGE_PRIORITY = 25;

    public const DECISION_MINIMUM_TRUST_PERCENTAGE = 60;
}