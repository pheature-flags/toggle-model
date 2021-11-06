<?php

declare(strict_types=1);

namespace Pheature\Model\Toggle;

use Pheature\Core\Toggle\Read\Segment;

/**
 * @psalm-import-type SegmentPayload from Segment
 */
final class StrictMatchingSegment implements Segment
{
    public const NAME = 'strict_matching_segment';
    private string $id;
    /** @var SegmentPayload */
    private array $criteria;

    /** @param SegmentPayload $criteria */
    public function __construct(string $id, array $criteria)
    {
        $this->id = $id;
        $this->criteria = $criteria;
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
        return $this->criteria;
    }

    public function match(array $payload): bool
    {
        $match = false;

        /** @var mixed $value */
        foreach ($this->criteria as $key => $value) {
            if (array_key_exists($key, $payload) && $value === $payload[$key]) {
                $match = true;
                continue;
            }

            return false;
        }

        return $match;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'type' => self::NAME,
            'criteria' => $this->criteria,
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
