<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Engine;

use Laminas\Barcode\Object\ObjectInterface as Barcode;

/**
 * Base class for GraphicsContext classes.
 * 
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
abstract class AbstractGraphicsContext implements GraphicsContext
{
    private array $methodInvocationsQueue = array();
    
    public function commit(): void
    {
        foreach($this->methodInvocationsQueue as $data)
        {
            list($method, $args) = $data;
            call_user_func_array(array($this, $method), $args);
        }
        
        $this->methodInvocationsQueue = array();
    }
    
    final protected function addToQueue($method, array $args = array())
    {
        $this->methodInvocationsQueue[] = array($method, $args);
    }
    
    public function clipRectangle($x1, $y1, $x2, $y2): void
    {
        $this->addToQueue('doClipRectangle', func_get_args());
    }

    abstract protected function doClipRectangle($x1, $y1, $x2, $y2);
    
    public function saveGS(): void
    {
        $this->addToQueue('doSaveGS');
    }
    
    abstract protected function doSaveGS();
    
    public function restoreGS(): void
    {
        $this->addToQueue('doRestoreGS');
    }
    
    abstract protected function doRestoreGS();
    
    public function drawImage(Image $image, $x1, $y1, $x2, $y2): void
    {
        if(!$image instanceof EmptyImage)
        {
            $this->addToQueue('doDrawImage', func_get_args());
        }
    }
    
    abstract protected function doDrawImage(Image $image, $x1, $y1, $x2, $y2);
    
    public function drawLine($x1, $y1, $x2, $y2): void
    {
        $this->addToQueue('doDrawLine', func_get_args());
    }
    
    abstract protected function doDrawLine($x1, $y1, $x2, $y2);
    
    public function setFillColor($colorData): void
    {
        $this->addToQueue('doSetFillColor', func_get_args());
    }
    
    abstract protected function doSetFillColor($colorData);
    
    public function setLineColor($colorData): void
    {
        $this->addToQueue('doSetLineColor', func_get_args());
    }
    
    abstract protected function doSetLineColor($colorData);
    
    public function drawPolygon(array $x, array $y, $type): void
    {
        $this->addToQueue('doDrawPolygon', func_get_args());
    }
    
    abstract protected function doDrawPolygon(array $x, array $y, $type);
    
    public function drawText($text, $x, $y, $encoding, $wordSpacing = 0, $fillType = self::SHAPE_DRAW_FILL): void
    {
        $this->addToQueue('doDrawText', func_get_args());
    }
    
    abstract protected function doDrawText($text, $x, $y, $encoding, $wordSpacing = 0, $fillType = self::SHAPE_DRAW_FILL);
    
    public function drawRoundedRectangle($x1, $y1, $x2, $y2, $radius, $fillType = self::SHAPE_DRAW_FILL_AND_STROKE): void
    {
        $this->addToQueue('doDrawRoundedRectangle', func_get_args());
    }
    
    abstract protected function doDrawRoundedRectangle($x1, $y1, $x2, $y2, $radius, $fillType = self::SHAPE_DRAW_FILL_AND_STROKE);
    
    public function setLineWidth($width): void
    {
        $this->addToQueue('doSetLineWidth', func_get_args());
    }
    
    abstract protected function doSetLineWidth($width);
    
    public function setLineDashingPattern($pattern): void
    {
        $this->addToQueue('doSetLineDashingPattern', func_get_args());
    }
    
    abstract protected function doSetLineDashingPattern($pattern);
    
    public function uriAction($x1, $y1, $x2, $y2, $uri): void
    {
        $this->addToQueue('doUriAction', func_get_args());
    }
    
    abstract protected function doUriAction($x1, $y1, $x2, $y2, $uri);
    
    public function goToAction(GraphicsContext $gc, $x1, $y1, $x2, $y2, $top): void
    {
        $this->addToQueue('doGoToAction', func_get_args());
    }
    
    abstract protected function doGoToAction(GraphicsContext $gc, $x1, $y1, $x2, $y2, $top);
    
    public function attachStickyNote($x1, $y1, $x2, $y2, $text): void
    {
        $this->addToQueue('doAttachStickyNote', func_get_args());
    }
    
    abstract protected function doAttachStickyNote($x1, $y1, $x2, $y2, $text);
    
    public function setAlpha($alpha): void
    {
        $this->addToQueue('doSetAlpha', func_get_args());
    }
    
    abstract protected function doSetAlpha($alpha);
    
    public function rotate($x, $y, $angle): void
    {
        $this->addToQueue('doRotate', func_get_args());
    }
    
    abstract protected function doRotate($x, $y, $angle);
    
    public function drawBarcode($x, $y, Barcode $barcode): void
    {
        $this->addToQueue('doDrawBarcode', array($x, $y, $barcode));
    }
    
    abstract protected function doDrawBarcode($x, $y, Barcode $barcode);
    
    public function drawEllipse($x, $y, $width, $height, $fillType = self::SHAPE_DRAW_FILL): void
    {
        $this->addToQueue('doDrawEllipse', array($x, $y, $width, $height, $fillType));
    }
    
    abstract protected function doDrawEllipse($x, $y, $width, $height, $fillType);
    
    public function drawArc($x, $y, $width, $height, $start, $end, $fillType = self::SHAPE_DRAW_FILL): void
    {
        $this->addToQueue('doDrawArc', array($x, $y, $width, $height, $start, $end, $fillType));
    }
    
    abstract protected function doDrawArc($x, $y, $width, $height, $start, $end, $fillType);
}
