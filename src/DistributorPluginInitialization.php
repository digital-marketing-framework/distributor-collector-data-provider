<?php

namespace DigitalMarketingFramework\Distributor\CollectorDataProvider;

use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Distributor\CollectorDataProvider\DataProvider\CollectorDataProvider;
use DigitalMarketingFramework\Distributor\Core\Registry\RegistryInterface;

class DistributorPluginInitialization
{
    public static function initialize(RegistryInterface $registry, CollectorInterface $collector): void
    {
        $registry->registerDataProvider(CollectorDataProvider::class, [$collector]);
    }
}
