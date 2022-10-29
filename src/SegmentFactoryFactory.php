<?php

declare(strict_types=1);

namespace Pheature\Model\Toggle;

use Pheature\Core\Toggle\Read\ChainSegmentFactory;
use Pheature\Model\Toggle\SegmentType\DateTimeIntervalSegmentFactory;
use Psr\Container\ContainerInterface;
use Pheature\Core\Toggle\Read\SegmentFactory as ReadSegmentFactory;
use StellaMaris\Clock\ClockInterface;
use Webmozart\Assert\Assert;

class SegmentFactoryFactory
{
    public function __invoke(ContainerInterface $container): ReadSegmentFactory
    {
        $clock = $container->get(ClockInterface::class);
        Assert::isInstanceOf($clock, ClockInterface::class);

        return new ChainSegmentFactory(
            new SegmentFactory(),
            new DateTimeIntervalSegmentFactory($clock)
        );
    }
}
