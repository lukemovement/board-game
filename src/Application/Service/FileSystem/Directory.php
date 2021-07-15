<?php

declare(strict_types=1);

namespace App\Application\Service\FileSystem;

use Symfony\Component\HttpKernel\KernelInterface;

class Directory {

    public function __construct(
        private string $path
    ) {
        if (false === file_exists($this->path)) {
            mkdir($path, 0777, true);
        }
    }

    public function getFile(
        string $name
    )
    {
        return new File(
            $this->path . "/" . $name
        );
    }
}