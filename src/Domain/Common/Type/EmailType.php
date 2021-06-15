<?php

namespace App\Domain\Common\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Throwable;

class EmailType extends Type
{
    const EMAIL = 'email'; 

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'text';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): EmailType|null
    {
        if (is_string($value)) {
            return new Email($value);
        }
        return null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->getEmail();
    }

    public function getName()
    {
        return self::EMAIL;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}