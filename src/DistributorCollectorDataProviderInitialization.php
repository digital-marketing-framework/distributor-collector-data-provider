<?php

namespace DigitalMarketingFramework\Distributor\CollectorDataProvider;

use DigitalMarketingFramework\Collector\Core\Registry\RegistryInterface as CollectorRegistryInterface;
use DigitalMarketingFramework\Core\Initialization;
use DigitalMarketingFramework\Core\Registry\RegistryDomain;
use DigitalMarketingFramework\Core\Registry\RegistryInterface;
use DigitalMarketingFramework\Distributor\CollectorDataProvider\DataProvider\CollectorDataProvider;
use DigitalMarketingFramework\Distributor\Core\DataProvider\DataProviderInterface;

class DistributorCollectorDataProviderInitialization extends Initialization
{
    protected const PLUGINS = [
        RegistryDomain::DISTRIBUTOR => [
            DataProviderInterface::class => [
                CollectorDataProvider::class,
            ],
        ],
    ];

    protected const SCHEMA_MIGRATIONS = [];

    public function __construct(
        protected CollectorRegistryInterface $collectorRegistry,
        string $packageAlias = ''
    ) {
        parent::__construct('distributor-collector-data-provider', '1.0.0', $packageAlias);
    }

    protected function getAdditionalPluginArguments(string $interface, string $pluginClass, RegistryInterface $registry): array
    {
        if ($pluginClass === CollectorDataProvider::class) {
            return [$this->collectorRegistry->getCollector()];
        }

        return parent::getAdditionalPluginArguments($interface, $pluginClass, $registry);
    }
}
