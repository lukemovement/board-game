<?php

namespace App\Domain\Common\Type;

use DateTime;
use DateTimeImmutable;

class DateRange {
    
    private DateTimeImmutable|null $startDate;
    private DateTimeImmutable|null $endDate;

    public function __construct(array $dates = []) {
        $this->startDate = $this->createDate(array_key_exists(0, $dates) ? $dates[0] : null);
        $this->endDate = $this->createDate(array_key_exists(1, $dates) ? $dates[1] : null);
    }

    private function createDate(DateTimeImmutable|DateTime|null|int|string $date): DateTimeImmutable|null {
        switch(true) {
            case is_string($date) || is_int($date):
                return new DateTimeImmutable($date);
            case $date instanceof DateTime:
                return DateTimeImmutable::createFromMutable($date);
            default:
                return $date;
        }
    }

    public function getStartDate(): DateTimeImmutable|null
    {
        return $this->startDate;
    }

    public function setStartDate(DateTimeImmutable|null $startDate): self
    {
        $this->startDate = $startDate;

        return $this;
    }

    public function getEndDate(): DateTimeImmutable|null
    {
        return $this->endDate;
    }

    public function setEndDate(DateTimeImmutable|null $endDate): self
    {
        $this->endDate = $endDate;

        return $this;
    }
}