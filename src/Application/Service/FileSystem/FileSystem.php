<?php

declare(strict_types=1);

namespace App\Application\Service\FileSystem;

use Symfony\Component\HttpKernel\KernelInterface;

class FileSystem {

    public function __construct(
        private KernelInterface $kernel
    ) {}

    public const DIRECTORY_RENDERED_MAPS = "/html-maps";

    public function getMapsDirectory()
    {
        return new Directory(
            $this->kernel->getProjectDir() . "/data" . self::DIRECTORY_RENDERED_MAPS,
        );
    }
}