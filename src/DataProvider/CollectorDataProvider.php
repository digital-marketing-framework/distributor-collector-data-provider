<?php

namespace DigitalMarketingFramework\Distributor\CollectorDataProvider\DataProvider;

use DigitalMarketingFramework\Collector\Core\Model\Configuration\CollectorConfiguration;
use DigitalMarketingFramework\Collector\Core\Service\CollectorInterface;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\ContainerSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\Custom\DataMapperGroupReferenceSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\CustomSchema;
use DigitalMarketingFramework\Core\SchemaDocument\Schema\SchemaInterface;
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
        $this->collector->prepareContext($configuration, context: $this->submission->getContext());
    }

    protected function process(): void
    {
        $configuration = CollectorConfiguration::convert($this->submission->getConfiguration());
        $data = $this->collector->collect($configuration, preparedContext: $this->submission->getContext());

        $dataMapperGroupId = $this->getConfig(static::KEY_DATA_MAP);
        $dataMapperGroupConfig = $this->submission->getConfiguration()->getDataMapperGroupConfiguration($dataMapperGroupId);
        $context = $this->dataProcessor->createContext($data, $this->submission->getConfiguration());
        $data = $this->dataProcessor->processDataMapperGroup($dataMapperGroupConfig, $context);
        foreach ($data as $field => $value) {
            $this->setField($field, $value);
        }
    }

    public static function getSchema(): SchemaInterface
    {
        /** @var ContainerSchema $schema */
        $schema = parent::getSchema();
        $schema->addProperty(static::KEY_DATA_MAP, new CustomSchema(DataMapperGroupReferenceSchema::TYPE));

        return $schema;
    }
}
