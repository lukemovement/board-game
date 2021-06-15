<?php

namespace App\Domain\Common\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Throwable;

class TimeType extends Type
{
    const TIME = 'time'; 

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'text';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Time|null
    {
        if (is_string($value)) {
            return new Time($value);
        }
        return null;
    }

    /** @param Time $value  */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return $value->__toString();
    }

    public function getName()
    {
        return self::TIME;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}