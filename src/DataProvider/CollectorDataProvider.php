<?php

namespace DigitalMarketingFramework\Distributor\CollectorDataProvider\DataProvider;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Distributor\Core\DataProvider\DataProvider;
use DigitalMarketingFramework\Distributor\Core\Model\DataSet\SubmissionDataSetInterface;
use DigitalMarketingFramework\Distributor\Core\Registry\RegistryInterface;

class CollectorDataProvider extends DataProvider
{
    protected const KEY_DATA_MAP = 'dataMap';
    protected const DEFAULT_DATA_MAP = null;

    public function __construct(
        string $keyword,
        RegistryInterface $registry,
        SubmissionDataSetInterface $submission,
        protected CollectorInterface $collector,
    ) {
        parent::__construct($keyword, $registry, $submission);
    }

    protected function processContext(ContextInterface $context): void
    {
        $configuration = CollectorConfiguration::convert($this->submission->getConfiguration());
        $this->collector->prepareContext($configuration, $this->submission->getContext());
    }

    protected function process(): void
    {
        $dataMap = $this->getConfig(static::KEY_DATA_MAP);
        $configuration = CollectorConfiguration::convert($this->submission->getConfiguration());
        $data = $this->collector->collect($configuration, $dataMap, $this->submission->getContext());
        foreach ($data as $field => $value) {
            $this->setField($field, $value);
        }
    }

    public static function getDefaultConfiguration(): array
    {
        return parent::getDefaultConfiguration() + [
            static::KEY_DATA_MAP => static::DEFAULT_DATA_MAP,
        ];
    }
}
