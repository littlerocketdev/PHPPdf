<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Engine\ZF;

use PHPPdf\Exception\RuntimeException;

use PHPPdf\Core\Engine\AbstractEngine;
use PHPPdf\Core\UnitConverter;
use PHPPdf\Util;
use PHPPdf\Exception\InvalidResourceException;
use PHPPdf\Core\Engine\GraphicsContext as BaseGraphicsContext;
use PHPPdf\Core\Engine\Engine as BaseEngine;
use LaminasPdf\PdfDocument;
use LaminasPdf\Outline\AbstractOutline;

/**
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class Engine extends AbstractEngine
{
    private static array $loadedEngines = array();
    
    private ?\LaminasPdf\PdfDocument $zendPdf;
    private array $colors = array();
    private array $images = array();
    private array $graphicsContexts = array();
    private array $outlines = array();
    
    public function __construct(PdfDocument $zendPdf = null, UnitConverter $unitConverter = null)
    {
        parent::__construct($unitConverter);
        $this->zendPdf = $zendPdf;
    }
    
    public function createGraphicsContext($graphicsContextSize, $encoding): \PHPPdf\Core\Engine\ZF\GraphicsContext
    {
        return new GraphicsContext($this, $graphicsContextSize, $encoding);
    }
    
    public function attachGraphicsContext(BaseGraphicsContext $gc): void
    {
        $this->getZendPdf()->pages[] = $gc->getPage();
        $this->graphicsContexts[] = $gc;
    }
    
    public function getAttachedGraphicsContexts()
    {
        return $this->graphicsContexts;
    }
    
    /**
     * @return Image
     */
    public function createImage($data)
    {
        $data = (string) $data;

        if(!isset($this->images[$data]))
        {
            $this->images[$data] = new Image($data, $this->unitConverter);
        }
        
        return $this->images[$data];
    }
    
    /**
     * @return Font
     */
    public function createFont($fontData): \PHPPdf\Core\Engine\ZF\Font
    {
        return new Font($fontData);
    }
    
    public function render()
    {
        $this->getZendPdf()->properties['Producer'] = sprintf('PHPPdf %s', \PHPPdf\Version::VERSION);
        
        foreach($this->graphicsContexts as $gc)
        {
            $gc->commit();
        }

        return $this->getZendPdf()->render();
    }
    
    /**
     * @return PdfDocument
     */
    public function getZendPdf()
    {
        if(!$this->zendPdf)
        {
            $this->zendPdf = new PdfDocument();
        }
        
        return $this->zendPdf;
    }
    
    /**
     * @internal
     */
    public function registerOutline($id, AbstractOutline $outline): void
    {
        $this->outlines[$id] = $outline;
    }
    
    /**
     * @internal
     */
    public function getOutline($id)
    {
        if(!isset($this->outlines[$id]))
        {
            throw new RuntimeException(sprintf('Bookmark with id "%s" dosn\'t exist.', $id));
        }
        
        return $this->outlines[$id];
    }
    
    public function loadEngine($file, $encoding)
    {
        if(isset(self::$loadedEngines[$file]))
        {
            return self::$loadedEngines[$file];
        }
        
        if(!is_readable($file))
        {
            throw InvalidResourceException::fileDosntExistException($file);
        }

        try
        {
            $pdf = PdfDocument::load($file);
            $engine = new self($pdf, $this->unitConverter);
            
            foreach($pdf->pages as $page)
            {
                $gc = new GraphicsContext($engine, $page, $encoding);
                $engine->attachGraphicsContext($gc);
            }
            
            self::$loadedEngines[$file] = $engine;
            
            return $engine;
        }
        catch(\LaminasPdf\Exception $e)
        {
            throw InvalidResourceException::invalidPdfFileException($file, $e);
        }
    }
    
    public function setMetadataValue($name, $value): void
    {
        switch($name)
        {
            case 'Trapped':
                $value = $value === 'null' ? null : Util::convertBooleanValue($value);
                $this->getZendPdf()->properties[$name] = $value;
                break;
            case 'CreationDate':
            case 'ModDate':
                $value = PdfDocument::pdfDate(strtotime($value));
                $this->getZendPdf()->properties[$name] = $value;
                break;
            case 'Title':
            case 'Author':
            case 'Subject':
            case 'Keywords':
            case 'Creator':
                $this->getZendPdf()->properties[$name] = $value;
                break;
        }
    }
    
    public function reset(): void
    {
        $this->graphicsContexts = array();
        $this->outlines = array();
        $this->zendPdf = null;
        $this->images = array();
    }
}
