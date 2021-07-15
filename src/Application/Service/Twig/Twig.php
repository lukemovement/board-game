<?php

declare(strict_types=1);

namespace App\Application\Service\Twig;

use Symfony\Component\HttpKernel\KernelInterface;

class Twig {

    public function __construct(
        private KernelInterface $kernel
    ) {}

    public function render(
        string $module,
        string $template,
        array $data
    )
    {
        $twig = new \Twig\Environment(
            new \Twig\Loader\FilesystemLoader(
                [$this->kernel->getProjectDir() . "/src/Domain/".$module."/Template"]
            )
        );

        $template = $twig->load($template . ".twig");

        return $template->render($data);
    }
}