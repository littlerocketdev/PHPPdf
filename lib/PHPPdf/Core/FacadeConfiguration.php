<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core;

/**
 * Configuration for Facade. Contains informations about config files.
 *
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class FacadeConfiguration
{
    private array $configFiles;

    public function __construct()
    {
        $this->configFiles = array(
            'node' => __DIR__.'/../Resources/config/nodes.xml',
            'complex-attribute' => __DIR__.'/../Resources/config/complex-attributes.xml',
            'font' => __DIR__.'/../Resources/config/fonts.xml',
        );
    }

    /**
     * Static constructor
     * 
     * @return FacadeConfiguration
     */
    public static function newInstance(): self
    {
        return new self();
    }

    /**
     * Set config file for populating node factory
     *
     * @param string $file
     * @return FacadeConfiguration
     */
    public function setNodesConfigFile($file): static
    {
        $this->configFiles['node'] = $file;

        return $this;
    }

    public function getNodesConfigFile()
    {
        return $this->configFiles['node'];
    }

    /**
     * Set config file for populating complex attribute factory
     *
     * @param string $file
     * @return FacadeConfiguration
     */
    public function setComplexAttributesConfigFile($file): static
    {
        $this->configFiles['complex-attribute'] = $file;

        return $this;
    }

    public function getComplexAttributesConfigFile()
    {
        return $this->configFiles['complex-attribute'];
    }
    
    /**
     * Set config file for populating font registry
     *
     * @param string $file
     * @return FacadeConfiguration
     */
    public function setFontsConfigFile($file): static
    {
        $this->configFiles['font'] = $file;

        return $this;
    }

    public function getFontsConfigFile()
    {
        return $this->configFiles['font'];
    }
}