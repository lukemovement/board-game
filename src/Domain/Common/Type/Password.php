<?php

namespace App\Domain\Common\Type;

use Exception;

class Password {
    
    private string $hash;

    public function __construct(string $password) {
        $this->setPassword($password);
    }

    public function getHash(): string
    {
        return $this->hash;
    }

    public function validate($password)
    {
        if (false === password_verify($password, $this->hash)) {
            throw new Exception("Invalid username or password");
        }
    }

    public function setPassword(string $password): self
    {
        $uppercase = preg_match('@[A-Z]@', $password);
        $lowercase = preg_match('@[a-z]@', $password);
        $number    = preg_match('@[0-9]@', $password);
        $specialChars = preg_match('@[^\w]@', $password);
        
        if (!$uppercase || !$lowercase || !$number || !$specialChars || strlen($password) < 8) {
            throw new Exception('Password should be at least 8 characters in length and should include at least one upper case letter, one number, and one special character.');
        }

        $this->hash = password_hash($password, null);

        return $this;
    }
}