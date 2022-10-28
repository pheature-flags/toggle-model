<?php

declare(strict_types=1);

namespace Pheature\Model\Toggle\Exception;

use Exception;

final class InvalidFactoryInstanceException extends Exception
{
    public static function invalidClock(): self
    {
        return  new self('Datetime Segments require to configure a clock for Segment factory.');
    }
}
