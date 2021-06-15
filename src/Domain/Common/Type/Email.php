<?php

namespace App\Domain\Common\Type;

use Exception;

class Email {
    
    private string $email;

    public function __construct(string $email) {
        $this->email = $email;

        if (false === filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email address");
        }
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}