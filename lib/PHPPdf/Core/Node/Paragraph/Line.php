<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Node\Paragraph;

use PHPPdf\Exception\InvalidArgumentException;
use PHPPdf\Core\Node\Node;
use PHPPdf\Core\Node\Paragraph;
use PHPPdf\Core\Point;
use PHPPdf\Core\Document;

/**
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class Line
{
    private array $parts = array();
    private $yTranslation;
    private $xTranslation;
    private ?Paragraph $paragraph;
    
    public function __construct(Paragraph $paragraph, $xTranslation, $yTranslation)
    {
        $this->xTranslation = $xTranslation;
        $this->yTranslation = $yTranslation;
        $this->paragraph = $paragraph;
    }
    
    public function addPart(LinePart $linePart): void
    {
        $linePart->setLine($this);
        $this->parts[] = $linePart;
    }
    
    public function addParts(array $parts): void
    {
        foreach($parts as $part)
        {
            $this->addPart($part);
        }
    }
    
    public function setYTranslation($translation): void
    {
        $this->yTranslation = $translation;
    }
    
    public function getYTranslation()
    {
        return $this->yTranslation;
    }
    
    public function setXTranslation($translation): void
    {
        $this->xTranslation = $translation;
    }
    
    public function getXTranslation()
    {
        return $this->xTranslation;
    }
    
    public function getParts(): array
    {
        return $this->parts;
    }
    
    public function setParagraph(Paragraph $paragraph): void
    {
        $this->paragraph = $paragraph;
    }
    
    private function getHorizontalTranslation(): int|float
    {
        $align = $this->paragraph->getRecurseAttribute('text-align');
        switch($align)
        {
            case Node::ALIGN_LEFT:
            case Node::ALIGN_JUSTIFY:
                return 0;
            case Node::ALIGN_RIGHT:
                return  $this->getRealWidth() - $this->getTotalWidth();
            case Node::ALIGN_CENTER:
                return ($this->getRealWidth() - $this->getTotalWidth())/2;
            default:
                throw new InvalidArgumentException(sprintf('Unsupported align type "%s".', $align));
        }
    }
    
    private function getRealWidth(): int|float
    {
        return $this->paragraph->getWidth() - $this->paragraph->getParentPaddingLeft() - $this->paragraph->getParentPaddingRight();
    }
    
    public function getTotalWidth(): int|float
    {
        $width = 0;
        foreach($this->parts as $part)
        {
            $width += $part->getWidth();
        }
        
        return $width;
    }
    
    /**
     * @return PHPPdf\Core\Point
     */
    public function getFirstPoint()
    {
        return $this->paragraph->getFirstPoint()->translate($this->xTranslation, $this->yTranslation);
    }
    
    public function getHeight()
    {
        $height = 0;
        
        foreach($this->parts as $part)
        {
            $height = max($height, $part->getHeight());
        }
        
        return $height;
    }
    
    public function format($formatJustify = true): void
    {
        $this->setXTranslation($this->getHorizontalTranslation());
        
        if(!$formatJustify || $this->paragraph->getRecurseAttribute('text-align') !== Node::ALIGN_JUSTIFY)
        {
            return;
        }

        $numberOfSpaces = $this->getNumberOfWords() - 1;
        
        $wordSpacing = $numberOfSpaces ? ($this->getRealWidth() - $this->getTotalWidth()) / $numberOfSpaces : null;

        $wordSpacingSum = 0;
        foreach($this->parts as $part)
        {
            $part->setWordSpacing($wordSpacing);
            $part->horizontalTranslate($wordSpacingSum);
            $wordSpacingSum += $part->getWordSpacingSum();
        }
    }
    
    private function getNumberOfWords(): int|float
    {
        $count = 0;
        
        foreach($this->parts as $part)
        {
            $count += $part->getNumberOfWords();
        }
        
        return $count;
    }
    
    public function flush(): void
    {
        foreach($this->parts as $part)
        {
            $part->flush();
        }
        
        $this->parts = array();
        $this->paragraph = null;
    }
}
