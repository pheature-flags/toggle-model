<?php

declare(strict_types=1);

namespace Pheature\Model\Toggle;

use Pheature\Core\Toggle\Read\Segment;
use StellaMaris\Clock\ClockInterface;

/**
 * @psalm-import-type Criteria from DateTimeIntervalCriteria
 */
class DateTimeIntervalStrictMatchingSegment implements Segment
{
    public const NAME = 'datetime_strict_matching_segment';
    private string $id;
    private DateTimeIntervalCriteria $criteria;
    private ClockInterface $clock;
    private StrictMatchingSegment $strictMatchingSegment;

    /** @param Criteria $criteria */
    public function __construct(string $id, array $criteria, ClockInterface $now)
    {
        $this->id = $id;
        $this->criteria = DateTimeIntervalCriteria::fromRawCriteria($criteria);
        $this->clock = $now;
        $this->strictMatchingSegment = new StrictMatchingSegment($id, $this->criteria->matches());
    }

    public function id(): string
    {
        return $this->id;
    }

    public function type(): string
    {
        return self::NAME;
    }

    public function criteria(): array
    {
        return $this->criteria->toArray();
    }

    public function match(array $payload): bool
    {
        if (false === $this->criteria->isOnTime($this->clock->now())) {
            return false;
        }

        return $this->strictMatchingSegment->match($payload);
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => self::NAME,
            'criteria' => $this->criteria->toArray(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
