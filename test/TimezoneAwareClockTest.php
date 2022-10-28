<?php

declare(strict_types=1);

namespace Pheature\Test\Model\Toggle;

use Pheature\Model\Toggle\TimezoneAwareClock;
use PHPUnit\Framework\TestCase;

class TimezoneAwareClockTest extends TestCase
{
    public function testItShouldGetCurrentTime(): void
    {
        $clock = new TimezoneAwareClock();

        $now = $clock->now();

        $this->assertSame(date_default_timezone_get(), $now->getTimezone()->getName());
    }

    public function testItShouldGetLocalizedCurrentTime(): void
    {
        $clock = new TimezoneAwareClock();

        $now = $clock->now('GMT');

        $this->assertSame('GMT', $now->getTimezone()->getName());
    }
}
