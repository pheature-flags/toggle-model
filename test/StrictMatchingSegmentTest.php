<?php

declare(strict_types=1);

namespace Pheature\Test\Model\Toggle;

use Generator;
use Pheature\Model\Toggle\StrictMatchingSegment;
use PHPUnit\Framework\TestCase;

final class StrictMatchingSegmentTest extends TestCase
{
    private const SEGMENT_ID = 'a_segment_id';

    /** @dataProvider nonMatchingPayloads */
    public function testItShouldNotMatch(array $criteria, array $payload): void
    {
        $segment = new StrictMatchingSegment(self::SEGMENT_ID, $criteria);

        $this->assertFalse($segment->match($payload));
        $this->assertSame(self::SEGMENT_ID, $segment->id());
        $this->assertSame('strict_matching_segment', $segment->type());
        $this->assertSame($criteria, $segment->criteria());
        $this->assertSame([
            'id' => self::SEGMENT_ID,
            'type' => 'strict_matching_segment',
            'criteria' => $criteria,
        ], $segment->jsonSerialize());
    }

    /** @dataProvider matchingPayloads */
    public function testItShouldMatch(array $criteria, array $payload): void
    {
        $segment = new StrictMatchingSegment(self::SEGMENT_ID, $criteria);

        $this->assertTrue($segment->match($payload));
        $this->assertSame(self::SEGMENT_ID, $segment->id());
        $this->assertSame('strict_matching_segment', $segment->type());
        $this->assertSame($criteria, $segment->criteria());
        $this->assertSame([
            'id' => self::SEGMENT_ID,
            'type' => 'strict_matching_segment',
            'criteria' => $criteria,
        ], $segment->jsonSerialize());
    }

    public function nonMatchingPayloads(): Generator
    {
        yield 'no criteria present' => [
            'criteria' => [],
            'payload' => [
                'location' => 'girona',
            ]
        ];

        yield 'different value comparison' => [
            'criteria' => [
                'location' => 'barcelona',
            ],
            'payload' => [
                'location' => 'girona',
            ]
        ];

        yield 'different key comparison' => [
            'criteria' => [
                'location' => 'barcelona',
            ],
            'payload' => [
                'city' => 'barcelona',
            ]
        ];
        yield 'strict matching multiple criteria with multiple fields' => [
            'criteria' => [
                'user_type' => 'top',
                'location' => 'barcelona',
            ],
            'payload' => [
                'location' => 'barcelona',
                'user_type' => 'default',
            ],
        ];

        yield 'strict matching multiple criteria with multiple fields and an incorrect one' => [
            'criteria' => [
                'user_type' => 'top',
                'location' => 'barcelona',
                'foo' => 'bar',
            ],
            'payload' => [
                'location' => 'barcelona',
                'user_type' => 'top',
                'foo' => 'baz',
            ],
        ];


    }

    public function matchingPayloads(): Generator
    {
        yield 'strict matching criteria' => [
            'criteria' => [
                'location' => 'barcelona',
            ],
            'payload' => [
                'location' => 'barcelona',
            ],
        ];
        yield 'strict matching criteria with multiple fields' => [
            'criteria' => [
                'location' => 'barcelona',
            ],
            'payload' => [
                'user_type' => 'top',
                'location' => 'barcelona',
            ],
        ];
        yield 'strict matching multiple criteria with multiple fields' => [
            'criteria' => [
                'user_type' => 'top',
                'location' => 'barcelona',
            ],
            'payload' => [
                'location' => 'barcelona',
                'user_type' => 'top',
            ],
        ];
    }
}
