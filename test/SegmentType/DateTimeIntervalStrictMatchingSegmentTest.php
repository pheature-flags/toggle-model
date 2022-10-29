<?php

declare(strict_types=1);

namespace Pheature\Test\Model\Toggle\SegmentType;

use DateTimeImmutable;
use DateTimeZone;
use Generator;
use InvalidArgumentException;
use Pheature\Model\Toggle\SegmentType\DateTimeIntervalStrictMatchingSegment;
use PHPUnit\Framework\TestCase;
use StellaMaris\Clock\ClockInterface;

class DateTimeIntervalStrictMatchingSegmentTest extends TestCase
{
    private const SEGMENT_ID = 'a_segment_id';

    /** @dataProvider invalidPayloads */
    public function testItShouldThrowInvalidArgumentExceptionWithInvalidStructure(array $criteria): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(0);
        $now = new DateTimeImmutable();
        new DateTimeIntervalStrictMatchingSegment(self::SEGMENT_ID, $criteria, $this->getClock($now));
    }

    /** @dataProvider nonMatchingPayloads */
    public function testItShouldNotMatch(DateTimeImmutable $now, array $criteria, array $payload): void
    {
        $segment = new DateTimeIntervalStrictMatchingSegment(self::SEGMENT_ID, $criteria, $this->getClock($now));

        $this->assertFalse($segment->match($payload));
        $this->assertSame(self::SEGMENT_ID, $segment->id());
        $this->assertSame('datetime_strict_matching_segment', $segment->type());
        $this->assertSame($criteria, $segment->criteria());
        $this->assertSame([
            'id' => self::SEGMENT_ID,
            'type' => 'datetime_strict_matching_segment',
            'criteria' => $criteria,
        ], $segment->jsonSerialize());

    }

    /** @dataProvider matchingPayloads */
    public function testItShouldMatch(DateTimeImmutable $now, array $criteria, array $payload): void
    {
        $segment = new DateTimeIntervalStrictMatchingSegment(self::SEGMENT_ID, $criteria, $this->getClock($now));

        $this->assertTrue($segment->match($payload));
        $this->assertSame(self::SEGMENT_ID, $segment->id());
        $this->assertSame('datetime_strict_matching_segment', $segment->type());
        $this->assertSame($criteria, $segment->criteria());
        $this->assertSame([
            'id' => self::SEGMENT_ID,
            'type' => 'datetime_strict_matching_segment',
            'criteria' => $criteria,
        ], $segment->jsonSerialize());
    }

    private function getClock(DateTimeImmutable $now): ClockInterface
    {
        return new class($now) implements ClockInterface {
            private DateTimeImmutable $now;

            public function __construct(DateTimeImmutable $now)
            {
                $this->now = $now;
            }

            public function now(): DateTimeImmutable
            {
                return $this->now;
            }
        };
    }

    public function invalidPayloads(): Generator
    {
        yield 'Missing start date time' => [
            'criteria' => [
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
                'matches' => [
                    'role' => 'customer'
                ]
            ]
        ];
        yield 'Empty start date time' => [
            'criteria' => [
                'start_datetime' => '',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
                'matches' => [
                    'role' => 'customer'
                ]
            ]
        ];
        yield 'Missing end date time' => [
            'criteria' => [
                'start_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
                'matches' => [
                    'role' => 'customer'
                ]
            ]
        ];
        yield 'Empty end date time' => [
            'criteria' => [
                'end_datetime' => '',
                'start_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
                'matches' => [
                    'role' => 'customer'
                ]
            ]
        ];
        yield 'Missing timezone' => [
            'criteria' => [
                'start_datetime' => '2022-10-28 11:30:00',
                'end_datetime' => '2022-10-28 11:30:00',
                'matches' => [
                    'role' => 'customer'
                ]
            ]
        ];
        yield 'Empty timezone' => [
            'criteria' => [
                'start_datetime' => '2022-10-28 11:30:00',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => '',
                'matches' => [
                    'role' => 'customer'
                ]
            ]
        ];
        yield 'Missing matches' => [
            'criteria' => [
                'start_datetime' => '2022-10-28 11:30:00',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
            ]
        ];
    }

    public function nonMatchingPayloads(): Generator
    {
        yield 'Before enable with matching criteria' => [
            'now' => new DateTimeImmutable('2022-10-27 11:30:00'),
            'criteria' => [
                'start_datetime' => '2022-10-27 11:30:01',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
                'matches' => [
                    'location' => 'Milan'
                ]
            ],
            'payload' => [
                'location' => 'Milan'
            ]
        ];

        yield 'After enable with matching criteria' => [
            'now' => new DateTimeImmutable('2022-10-28 11:30:01'),
            'criteria' => [
                'start_datetime' => '2022-10-27 11:30:00',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
                'matches' => [
                    'role' => 'customer'
                ]
            ],
            'payload' => [
                'role' => 'customer'
            ]
        ];

        yield 'In time with no matching criteria' => [
            'now' => new DateTimeImmutable('2022-10-28 03:30:00'),
            'criteria' => [
                'start_datetime' => '2022-10-27 11:30:00',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
                'matches' => [
                    'location' => 'Barcelona',
                    'role' => 'partner',
                    'group' => 'standard',
                ]
            ],
            'payload' => [
                'location' => 'Barcelona',
                'role' => 'customer',
                'group' => 'standard',
            ]
        ];
    }

    public function matchingPayloads(): Generator
    {
        yield 'Matches in time with single parameter matches' => [
            'now' => new DateTimeImmutable('2022-10-27 11:30:00', new DateTimeZone('GMT')),
            'criteria' => [
                'start_datetime' => '2022-10-27 11:30:00',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'GMT',
                'matches' => [
                    'location' => 'Milan'
                ]
            ],
            'payload' => [
                'location' => 'Milan'
            ]
        ];
        yield 'Matches in time with multiple parameter matches' => [
            'now' => new DateTimeImmutable('2022-10-28 11:30:00'),
            'criteria' => [
                'start_datetime' => '2022-10-27 11:30:00',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'UTC',
                'matches' => [
                    'location' => 'Milan',
                    'role' => 'customer',
                ]
            ],
            'payload' => [
                'location' => 'Milan',
                'role' => 'customer',
            ]
        ];

        yield 'Matches in time localized with single parameter matches' => [
            'now' => new DateTimeImmutable('2022-10-27 11:30:01', new DateTimeZone('Europe/London')),
            'criteria' => [
                'start_datetime' => '2022-10-27 11:30:00',
                'end_datetime' => '2022-10-28 11:30:00',
                'timezone' => 'Europe/London',
                'matches' => [
                    'location' => 'Birmingham',
                ]
            ],
            'payload' => [
                'location' => 'Birmingham',
            ]
        ];

    }
}
