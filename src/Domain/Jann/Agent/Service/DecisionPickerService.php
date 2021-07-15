<?php

declare(strict_types=1);

namespace App\Domain\Jann\Agent\Service;

use App\Domain\Jann\Agent\Dto\DecisionDto;
use App\Domain\Jann\NeuralNetworkConfig;

class DecisionPickerService {

    /**
     * Select the best decision
     * 
     * @param DecisionDto[][] $decisionsCollections
     */
    public function execute(array $decisionsCollections): DecisionDto|null
    {
        $valueDicisionsCollections = [];
            $minChance = null;
            $maxChance = null;

            foreach($decisionsCollections as $decisions) {
                $rewardsTotal = 0;
                $chanceTotal = 0;
                foreach($decisions as $index => $decision) {
                    $rewardsTotal = $rewardsTotal + $decision->reward;
                    $chanceTotal = $chanceTotal * $decision->chance;

                }

                if ($chanceTotal < $minChance || null === $minChance) {
                    $minChance = $chanceTotal;
                }

                if ($chanceTotal > $maxChance || null === $maxChance) {
                    $maxChance = $chanceTotal;
                }

                $valueDicisionsCollections[] = [
                    "decisions" => $decisions,
                    "reward" => $rewardsTotal,
                    "chance" => $chanceTotal 
                ];
            }

            $requiredTrust = ($maxChance - $minChance) / 100 * NeuralNetworkConfig::DECISION_MINIMUM_TRUST_PERCENTAGE;
            $selectedDecisionCollection = null;

            foreach ($decisionsCollections as $trust => $decisionsCollection) {
                if ($trust >= $requiredTrust) {
                    $potentialDecisions[] = $decisionsCollection[0];
                    if (
                        null === $selectedDecisionCollection ||
                        $selectedDecisionCollection["reward"] < $decisionsCollection["reward"]
                    ) {
                        $selectedDecisionCollection = $decisionsCollection;
                    }
                }
            }

            if (null === $selectedDecisionCollection && count($decisionsCollections) > 0) {
                $selectedDecisionCollection = array_rand($decisionsCollections)["decisions"][0];
            }

            return $selectedDecisionCollection;
    }

}