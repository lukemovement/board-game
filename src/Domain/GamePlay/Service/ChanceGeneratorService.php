<?php

namespace App\Domain\GamePlay\Service;

class ChanceGeneratorService {

    public function execute(
        int $chance,
        int $outof
    )
    {
        return rand(0, $outof) <= $chance;
    }
}