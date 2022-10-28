<?php

declare(strict_types=1);

namespace Pheature\Model\Toggle;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;
use Webmozart\Assert\Assert;

/**
 * @psalm-type Criteria array{
 *   timezone: string,
 *   start_datetime: string,
 *   end_datetime: string,
 *   matches: array<string, mixed>
 * }
 */
class DateTimeIntervalCriteria
{
    private const DATE_TIME_FORMAT = 'Y-m-d H:i:s';
    private DateTimeImmutable $startTime;
    private DateTimeImmutable $endTime;
    /** @var array<string, mixed>  */
    private array $matches;

    /** @param array<string, mixed> $matches */
    private function __construct(
        DateTimeImmutable $startTime,
        DateTimeImmutable $endTime,
        array $matches
    ) {
        $this->startTime = $startTime;
        $this->endTime = $endTime;
        $this->matches = $matches;
    }

    /** @param Criteria $criteria */
    public static function fromRawCriteria(array $criteria): self
    {
        Assert::keyExists($criteria, 'start_datetime');
        Assert::notEmpty($criteria['start_datetime']);
        Assert::keyExists($criteria, 'end_datetime');
        Assert::notEmpty($criteria['end_datetime']);
        Assert::keyExists($criteria, 'timezone');
        Assert::keyExists($criteria, 'matches');
        try {
            $timezone = new DateTimeZone($criteria['timezone']);
        } catch (\Exception $exception) {
            throw new InvalidArgumentException(
                sprintf('Invalid timezone "%s" given.', $criteria['timezone']),
                (int)$exception->getCode(),
                $exception
            );
        }

        return new self(
            new DateTimeImmutable($criteria['start_datetime'], $timezone),
            new DateTimeImmutable($criteria['end_datetime'], $timezone),
            $criteria['matches'],
        );
    }

    /** @return array<string, mixed> */
    public function matches(): array
    {
        return $this->matches;
    }

    public function isOnTime(DateTimeImmutable $now): bool
    {
        if ($now < $this->startTime) {
            return false;
        }

        if ($now > $this->endTime) {
            return false;
        }

        return true;
    }

    /** @return Criteria */
    public function toArray(): array
    {
        return [
            'start_datetime' => $this->startTime->format(self::DATE_TIME_FORMAT),
            'end_datetime' => $this->endTime->format(self::DATE_TIME_FORMAT),
            'timezone' => $this->startTime->getTimezone()->getName(),
            'matches' => $this->matches,
        ];
    }
}
