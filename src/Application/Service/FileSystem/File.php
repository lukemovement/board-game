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
    ): int|false
    {
        return file_put_contents($this->path, $data);
    }

    public function read(): string|false
    {
        return file_get_contents($this->path);
    }

    public function exists(): bool
    {
        return file_exists($this->path);
    }

    public function writable(): bool
    {
        return is_writeable($this->path);
    }

    public function getPath(): string
    {
        return $this->path;
    }
}