<?php

declare(strict_types=1);

namespace App\Application\Service\FileSystem;

use Symfony\Component\HttpKernel\KernelInterface;

class File {

    public function __construct(
        private string $path
    ) {}

    public function write(
        string $data
    )
    {
        file_put_contents($this->path, $data);
    }

    public function read()
    {
        file_get_contents($this->path);
    }

    public function exists()
    {
        file_exists($this->path);
    }

    public function writable()
    {
        is_writeable($this->path);
    }
}