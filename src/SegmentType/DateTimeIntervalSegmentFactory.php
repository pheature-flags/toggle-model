<?php

declare(strict_types=1);

namespace Pheature\Model\Toggle\SegmentType;

use Pheature\Core\Toggle\Exception\InvalidSegmentTypeGiven;
use Pheature\Core\Toggle\Read\Segment;
use Pheature\Core\Toggle\Read\SegmentFactory;
use StellaMaris\Clock\ClockInterface;

class DateTimeIntervalSegmentFactory implements SegmentFactory
{
    private ClockInterface $clock;

    public function __construct(ClockInterface $clock)
    {
        $this->clock = $clock;
    }

    public function create(string $segmentId, string $segmentType, array $criteria): Segment
    {
        if (DateTimeIntervalStrictMatchingSegment::NAME === $segmentType) {
            return new DateTimeIntervalStrictMatchingSegment($segmentId, $criteria, $this->clock);
        }

        throw InvalidSegmentTypeGiven::withType($segmentType);
    }

    public function types(): array
    {
        return [
            DateTimeIntervalStrictMatchingSegment::NAME
        ];
    }
}
