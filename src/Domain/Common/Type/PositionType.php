<?php

namespace App\Domain\Common\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Throwable;

class PositionType extends Type
{
    const POSITION = 'position'; 

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'TEXT';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): Position|null
    {
        if (is_string($value)) {
            return new Position(json_decode($value));
        }
        return null;
    }

    /** @param Position $value */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode([$value->getRow(), $value->getColumn()]);
    }

    public function getName()
    {
        return self::POSITION;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}