<?php

declare(strict_types=1);

namespace Pheature\Model\Toggle;

use DateTimeImmutable;
use DateTimeZone;
use StellaMaris\Clock\ClockInterface;

final class TimezoneAwareClock implements ClockInterface
{
    public function now(?string $timezone = null): DateTimeImmutable
    {
        if (null === $timezone) {
            return new DateTimeImmutable();
        }

        return new DateTimeImmutable('now', new DateTimeZone($timezone));
    }
}
