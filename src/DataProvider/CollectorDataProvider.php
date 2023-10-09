<?php

namespace DigitalMarketingFramework\Distributor\CollectorDataProvider\DataProvider;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\Plugin\DataProcessor\DataMapperSchema;
use DigitalMarketingFramework\Core\ConfigurationDocument\SchemaDocument\Schema\SchemaInterface;
use DigitalMarketingFramework\Core\Context\ContextInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareInterface;
use DigitalMarketingFramework\Core\DataProcessor\DataProcessorAwareTrait;
use DigitalMarketingFramework\Distributor\Core\DataProvider\DataProvider;
use DigitalMarketingFramework\Distributor\Core\Model\DataSet\SubmissionDataSetInterface;
use DigitalMarketingFramework\Distributor\Core\Registry\RegistryInterface;

class CollectorDataProvider extends DataProvider implements DataProcessorAwareInterface
{
    use DataProcessorAwareTrait;

    protected const KEY_DATA_MAP = 'dataMap';

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
        $configuration = CollectorConfiguration::convert($this->submission->getConfiguration());
        $data = $this->collector->collect($configuration, $this->submission->getContext());
        $data = $this->dataProcessor->processDataMapper($this->getConfig(static::KEY_DATA_MAP), $data, $this->submission->getConfiguration());
        foreach ($data as $field => $value) {
            $this->setField($field, $value);
        }
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_DATA_MAP, new CustomSchema(DataMapperSchema::TYPE));

        return $schema;
    }
}
