<?php

declare(strict_types=1);

namespace Pheature\Model\Toggle;

use Pheature\Core\Toggle\Read\Segment;

/**
 * @psalm-import-type SegmentPayload from Segment
 */
final class IdentitySegment implements Segment
{
    public const NAME = 'identity_segment';
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
        if (false === array_key_exists('identity_id', $payload)) {
            return false;
        }

        /** @var string $identityId */
        $identityId = $payload['identity_id'];

        return in_array($identityId, $this->criteria, true);
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
