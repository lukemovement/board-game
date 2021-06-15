<?php

namespace App\Domain\Common\Type;

use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Throwable;

class DateRangeType extends Type
{
    const DATE_RANGE = 'date_range'; 

    public function getSQLDeclaration(array $fieldDeclaration, AbstractPlatform $platform)
    {
        return 'text';
    }

    public function convertToPHPValue($value, AbstractPlatform $platform): DateRange|null
    {
        if (is_string($value)) {
            return new DateRange(json_decode($value));
        }
        return null;
    }

    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {
        return json_encode([$value->getStartDate()->getTimestamp(), $value->getEndDate()->getTimestamp()]);
    }

    public function getName()
    {
        return self::DATE_RANGE;
    }

    /**
     * {@inheritdoc}
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}