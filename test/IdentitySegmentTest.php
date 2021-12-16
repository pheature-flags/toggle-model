<?php

declare(strict_types=1);

namespace Pheature\Test\Model\Toggle;

use Generator;
use Pheature\Model\Toggle\IdentitySegment;
use PHPUnit\Framework\TestCase;

final class IdentitySegmentTest extends TestCase
{
    private const SEGMENT_ID = 'a_segment_id';

    /** @dataProvider nonMatchingPayloads */
    public function testItShouldNotMatch(array $criteria, array $payload): void
    {
        $segment = new IdentitySegment(self::SEGMENT_ID, $criteria);

        $this->assertFalse($segment->match($payload));
        $this->assertSame(self::SEGMENT_ID, $segment->id());
        $this->assertSame('identity_segment', $segment->type());
        $this->assertSame($criteria, $segment->criteria());
        $this->assertSame([
            'id' => self::SEGMENT_ID,
            'type' => 'identity_segment',
            'criteria' => $criteria,
        ], $segment->jsonSerialize());
    }

    /** @dataProvider matchingPayloads */
    public function testItShouldMatch(array $criteria, array $payload): void
    {
        $segment = new IdentitySegment(self::SEGMENT_ID, $criteria);

        $this->assertTrue($segment->match($payload));
        $this->assertSame(self::SEGMENT_ID, $segment->id());
        $this->assertSame('identity_segment', $segment->type());
        $this->assertSame($criteria, $segment->criteria());
        $this->assertSame([
            'id' => self::SEGMENT_ID,
            'type' => 'identity_segment',
            'criteria' => $criteria,
        ], $segment->jsonSerialize());
    }

    public function nonMatchingPayloads(): Generator
    {
        yield 'no criteria present' => [
            'criteria' => [],
            'payload' => [
                'identity_id' => 'some_identity',
            ]
        ];

        yield 'no identity present' => [
            'criteria' => [
                'identity_id' => 'some_identity',
            ],
            'payload' => [
                'other' => 'some_identity',
            ]
        ];

        yield 'no matching identity_id present' => [
            'criteria' => [
                'identity_id' => ['some_identity'],
            ],
            'payload' => [
                'identity_id' => 'some_other_identity',
            ]
        ];
    }

    public function matchingPayloads(): Generator
    {
        yield 'matching identity_id present' => [
            'criteria' => [
                'identity_id' => 'some_identity',
            ],
            'payload' => [
                'identity_id' => 'some_identity',
            ]
        ];
    }
}
