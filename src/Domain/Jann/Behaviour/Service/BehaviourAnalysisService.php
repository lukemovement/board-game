<?php

declare(strict_types=1);

namespace App\Domain\Jann\Behaviour\Service;

use App\Domain\Jann\Agent\Dto\DecisionDto;
use App\Domain\Jann\Behaviour\Entity\Behaviour;
use Doctrine\Common\Collections\ArrayCollection;

class BehaviourAnalysisService {

    private ArrayCollection $allBehaviours;
    /**
     * @param Behaviour[][] $behaviourPredictions
     * 
     * @return DecisionDto[][]
     */
    public function execute(array $behaviourPredictions): array
    {
        $this->allBehaviours = new ArrayCollection(...$behaviourPredictions);

        return (new ArrayCollection($behaviourPredictions))->map(function(array $behaviourPath)
        {
            return (new ArrayCollection($behaviourPath))->map(function(Behaviour $behaviour) {
                $decision = new DecisionDto();

                $decision->chance = $this->calculateBehaviourTrust($behaviour);
                $decision->reward = $behaviour->getBehaviourReward();
                $decision->behaviour = $behaviour;

                return $decision;
            }); 
        })->toArray();
    }

    private function calculateBehaviourTrust(Behaviour $selectedBehaviour)
    {
        $highestLinkCount = 0;

        $this->allBehaviours
            ->filter(fn(Behaviour $behaviour) => $behaviour->environmentMatches($selectedBehaviour))
            ->forAll(function(int $i, Behaviour $behaviour) use (&$highestLinkCount)
            {
                $highestLinkCount = $highestLinkCount < $behaviour->getLinkCount() ?  $behaviour->getLinkCount() : $highestLinkCount;

                return true;
            });

        return $highestLinkCount / $selectedBehaviour->getLinkCount();
    }

}