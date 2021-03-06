<?php
declare(strict_types=1);

namespace SmartDevs\ElastiCommerce\Index\Type;

use SmartDevs\ElastiCommerce\Config\IndexConfig;
use SmartDevs\ElastiCommerce\Implementor\Index\Type\MappingImplementor;
use SmartDevs\ElastiCommerce\Index\Type\Mapping\DynamicTemplateCollection;
use SmartDevs\ElastiCommerce\Index\Type\Mapping\Field\FieldCollection;

class Mapping implements MappingImplementor
{

    /**
     * @var bool
     */
    protected $isInitialized = null;

    /**
     * @var IndexConfig
     */
    protected $indexConfig = null;

    /**
     * list of dynamic templates
     *
     * @var DynamicTemplateCollection
     */
    protected $dynamicTemplates = null;

    /**
     * mapping fields
     *
     * @var FieldCollection
     */
    protected $mapping = null;

    /**
     * Mappings constructor.
     *
     * @param Config $config
     */
    public function __construct(IndexConfig $indexConfig)
    {
        $this->indexConfig = $indexConfig;
        $this->isInitialized = false;
    }

    /**
     * set flag the class is initialized (lazy loading)
     *
     * @param bool $value
     * @return Mapping
     */
    protected function setIsInitialized(bool $flag): Mapping
    {
        $this->isInitialized = $flag;
        return $this;
    }

    /**
     * checks if class initialized (lazy loading)
     *
     * @return bool
     */
    protected function isInitialized(): bool
    {
        return $this->isInitialized;
    }

    /**
     * initialize class
     *
     * @return Mapping
     */
    protected function initialize(): Mapping
    {
        $this->readMappingFromConfigFile();
        $this->setIsInitialized(true);
        return $this;
    }

    /**
     * check a given file is valid and readable
     *
     * @param $filename
     * @return bool
     */
    protected function isConfigFileReadable($filename): bool
    {
        return file_exists($filename) && is_readable($filename) && false === is_dir($filename);
    }

    /**
     * init mapping from config file
     *
     * @return Mapping
     * @throws \Exception
     */
    protected function readMappingFromConfigFile(): Mapping
    {
        $configFile = $this->indexConfig->getSchemaConfigFile();
        if (true === $this->isConfigFileReadable($configFile)) {
            $xml = simplexml_load_file($configFile);
        }
        if (false === isset($xml) || false === $xml instanceof \SimpleXMLElement) {
            throw new \Exception('missing valid schema config file');
        }
        $this->initMappingsFromXml($xml);
        return $this;
    }

    /**
     * init dynamic templates and mapping
     *
     * @param \SimpleXMLElement $xml
     * @return Mapping
     */
    protected function initMappingsFromXml(\SimpleXMLElement $xml): Mapping
    {
        //init dynamic templates
        $this->dynamicTemplates = new DynamicTemplateCollection();
        if (true === property_exists($xml, 'dynamic_templates')) {
            $this->dynamicTemplates->setXmlConfig($xml->dynamic_templates->children());
        }
        //init mapping fields
        $this->mapping = new FieldCollection();
        if (true === property_exists($xml, 'mapping')) {
            $this->mapping->setName('properties');
            $this->mapping->setXmlConfig($xml->mapping);
        }
        return $this;
    }

    /**
     * get collection of dynamic templates
     *
     * @return DynamicTemplateCollection
     */
    public function getDynamicTemplates(): DynamicTemplateCollection
    {
        if (false === $this->isInitialized()) {
            $this->initialize();
        }
        return $this->dynamicTemplates;
    }

    public function getFields(): FieldCollection
    {
        if (false === $this->isInitialized()) {
            $this->initialize();
        }
        return $this->mapping;
    }

    /**
     * get index mapping
     *
     * @return FieldCollection
     */
    public function getMapping(): FieldCollection
    {
        if (false === $this->isInitialized()) {
            $this->initialize();
        }
        return $this->mapping;
    }
}