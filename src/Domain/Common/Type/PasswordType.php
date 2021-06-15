<?php

namespace App\Domain\Common\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Throwable;

class PasswordType extends Type
{
    const PASSWORD = 'password'; 

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'text';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Password|null
    {
        if (is_string($value)) {
            return new Password($value);
        }
        return null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->getHash();
    }

    public function getName()
    {
        return self::PASSWORD;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}