<?php

declare(strict_types=1);

namespace Pheature\Test\Model\Toggle;

use Beste\Clock\SystemClock;
use Pheature\Model\Toggle\SegmentFactoryFactory;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use StellaMaris\Clock\ClockInterface;

class SegmentFactoryFactoryTest extends TestCase
{
    public function testItShouldReturnAChainSegmentFactoryAwareOfAvailableSegmentTypes(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())
            ->method('get')
            ->with(ClockInterface::class)
            ->willReturn(SystemClock::create());

        $segmentFactoryFactory = new SegmentFactoryFactory();

        $segmentFactory = $segmentFactoryFactory->__invoke($container);
        $this->assertSame([
            "strict_matching_segment",
            "identity_segment",
            "in_collection_matching_segment",
            "datetime_strict_matching_segment",
        ], $segmentFactory->types());
    }
}
