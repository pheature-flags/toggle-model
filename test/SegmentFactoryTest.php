<?php

declare(strict_types=1);

namespace Pheature\Test\Model\Toggle;

use Pheature\Core\Toggle\Exception\InvalidSegmentTypeGiven;
use Pheature\Model\Toggle\DateTimeIntervalStrictMatchingSegment;
use Pheature\Model\Toggle\IdentitySegment;
use Pheature\Model\Toggle\InCollectionMatchingSegment;
use Pheature\Model\Toggle\StrictMatchingSegment;
use Pheature\Model\Toggle\SegmentFactory;
use Pheature\Model\Toggle\TimezoneAwareClock;
use PHPUnit\Framework\TestCase;

final class SegmentFactoryTest extends TestCase
{
    private const SEGMENT_ID = 'some_segment';
    private const STRICT_MATCHING_SEGMENT = 'strict_matching_segment';
    private const DATETIME_STRICT_MATCHING_SEGMENT = 'datetime_strict_matching_segment';
    private const IDENTITY_SEGMENT = 'identity_segment';
    private const IN_COLLECTION_MATCHING_SEGMENT = 'in_collection_matching_segment';
    private const CRITERIA = [
        'some' => 'criteria',
        'with' => [
            'mixed' => 'values',
        ],
    ];
    private const DATETIME_CRITERIA = [
        'start_datetime' => '2022-10-28 12:39:00',
        'end_datetime' => '2022-10-29 12:39:00',
        'timezone' => 'UTC',
        'matches' => [
            'mixed' => 'values',
        ],
    ];

    public function testItShouldKnownAvailableSegmentTypes(): void
    {
        $factory = new SegmentFactory();

        $this->assertSame([
            self::STRICT_MATCHING_SEGMENT,
            self::IDENTITY_SEGMENT,
            self::IN_COLLECTION_MATCHING_SEGMENT,
            self::DATETIME_STRICT_MATCHING_SEGMENT,
        ], $factory->types());
    }

    public function testItShouldThrowAnExceptionWithUnknownStrategyTypes(): void
    {
        $this->expectException(InvalidSegmentTypeGiven::class);
        $factory = new SegmentFactory();

        $factory->create(self::SEGMENT_ID, 'unknown_strategy_type', self::CRITERIA);
    }

    public function testItShouldCreateInstancesOfStrictMatchingSegment(): void
    {
        $factory = new SegmentFactory();

        $segment = $factory->create(self::SEGMENT_ID, self::STRICT_MATCHING_SEGMENT, self::CRITERIA);
        $this->assertInstanceOf(StrictMatchingSegment::class, $segment);
    }

    public function testItShouldThrowInvalidFactoryInstanceException(): void
    {
        $this->expectException(\Pheature\Model\Toggle\Exception\InvalidFactoryInstanceException::class);
        $factory = new SegmentFactory();

        $factory->create(self::SEGMENT_ID, self::DATETIME_STRICT_MATCHING_SEGMENT, self::DATETIME_CRITERIA);
    }

    public function testItShouldCreateInstancesOfDateTimeIntervalStrictMatchingSegment(): void
    {
        $factory = new SegmentFactory(new TimezoneAwareClock());

        $segment = $factory->create(self::SEGMENT_ID, self::DATETIME_STRICT_MATCHING_SEGMENT, self::DATETIME_CRITERIA);
        $this->assertInstanceOf(DateTimeIntervalStrictMatchingSegment::class, $segment);
    }

    public function testItShouldCreateInstancesOfIdentitySegment(): void
    {
        $factory = new SegmentFactory();

        $segment = $factory->create(self::SEGMENT_ID, self::IDENTITY_SEGMENT, self::CRITERIA);
        $this->assertInstanceOf(IdentitySegment::class, $segment);
    }

    public function testItShouldCreateInstancesOfInCollectionMatchingSegment(): void
    {
        $factory = new SegmentFactory();

        $segment = $factory->create(self::SEGMENT_ID, self::IN_COLLECTION_MATCHING_SEGMENT, self::CRITERIA);
        $this->assertInstanceOf(InCollectionMatchingSegment::class, $segment);
    }
}
