<?php

namespace App\Domain\Common\Type;

class Time {
    public function __construct(private string $time) {}

    public function __toString(): string
    {
        return $this->time;
    }
}