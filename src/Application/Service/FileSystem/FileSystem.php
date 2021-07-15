<?php

declare(strict_types=1);

namespace App\Application\Service\FileSystem;

use App\Domain\GamePlay\Entity\Game;
use Symfony\Component\HttpKernel\KernelInterface;

class FileSystem {

    public function __construct(
        private KernelInterface $kernel
    ) {}

    public function getMapsDirectory()
    {
        return new Directory(
            $this->kernel->getProjectDir() . "/data/html-maps",
        );
    }

    public function getJannsTrainingDirectory(Game $game)
    {
        return new Directory(
            $this->kernel->getProjectDir() . "/data/jann/training/" . $game->getId(),
        );
    }
}