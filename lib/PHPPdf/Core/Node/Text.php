<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Node;

use PHPPdf\Exception\InvalidArgumentException;
use PHPPdf\Core\DrawingTaskHeap;
use PHPPdf\Core\Node\Node;
use PHPPdf\Core\UnitConverter;
use PHPPdf\Core\Node\Paragraph\LinePart;
use PHPPdf\Core\Formatter\Formatter;
use PHPPdf\Core\Document;
use PHPPdf\Core\Point;
use PHPPdf\Core\DrawingTask;

/**
 * Text node
 * 
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class Text extends Node
{   
    private $text;
    private ?\PHPPdf\Core\Node\TextTransformator $textTransformator = null;
    
    private array $words = array();
    private array $wordsSizes = array();
    private array $pointsOfWordsLines = array();
    
    protected $lineParts = array();
    
    private array $childTexts = array();

    public function __construct($text = '', array $attributes = array(), UnitConverter $converter = null)
    {
        $this->setText($text);
        
        parent::__construct($attributes, $converter);
    }

    public function initialize(): void
    {
        parent::initialize();
        
        $this->setAttribute('text-align', null);
    }
    
    public function setTextTransformator(TextTransformator $transformator): void
    {
        $this->textTransformator = $transformator;
    }

    public function setText($text): void
    {
        if($this->textTransformator !== null)
        {
            $text = $this->textTransformator->transform($text);
        }
        
        $this->text = (string) $text;
    }
    
    protected function beforeFormat(Document $document)
    {
        foreach($this->childTexts as $text)
        {
            $text->beforeFormat($document);
            
            $this->text .= $text->getText();
        }
        
        $this->childTexts = array();
    }

    public function getText()
    {
        return $this->text;
    }
    
    public function addLineOfWords(array $words, $widthOfLine, Point $point): void
    {
        $this->wordsInRows[] = $words;
        $this->lineSizes[] = $widthOfLine;
        $this->pointsOfWordsLines[] = $point;
    }

    public function getMinWidth()
    {
        $minWidth = 0;
        foreach($this->lineParts as $part)
        {
            $minWidth = max($minWidth, $part->getWidth());
        }
        return $minWidth;
    }

    protected function doDraw(Document $document, DrawingTaskHeap $tasks)
    {
        foreach($this->lineParts as $part)
        {
            $part->collectOrderedDrawingTasks($document, $tasks);
        }
    }

    protected function doBreakAt($height)
    {
        $clone = null;
        if($height > 0)
        {
            $clone = $this->copy();

            $this->setAttribute('padding-bottom', 0);
            $this->setAttribute('margin-bottom', 0);

            $clone->setAttribute('padding-top', 0);
            $clone->setAttribute('margin-top', 0);

            $startDrawingPoint = $this->getFirstPoint();
            $oldHeight = $this->getHeight();
            $this->setHeight($height);
            $this->reorganize($startDrawingPoint);
            $endDrawingPoint = $this->getDiagonalPoint();
            $clone->setHeight($oldHeight - $height);
            $clone->reorganize($endDrawingPoint->translate(-$this->getWidth(), 0));
        }

        return $clone;
    }

    private function reorganize(Point $leftTopCornerPoint): void
    {
        $boundary = $this->getBoundary();
        $boundary->reset();
        $boundary->setNext($leftTopCornerPoint)
                 ->setNext($leftTopCornerPoint->translate($this->getWidth(), 0))
                 ->setNext($leftTopCornerPoint->translate($this->getWidth(), $this->getHeight()))
                 ->setNext($leftTopCornerPoint->translate(0, $this->getHeight()))
                 ->close();
    }

    public function add(Node $node): void
    {
        if(!$node instanceof Text)
        {
            return;
        }
        $this->childTexts[] = $node;
    }
    
    public function setWordsSizes(array $words, array $sizes): void
    {
        if(count($words) != count($sizes))
        {
            throw new InvalidArgumentException(sprintf('Words and sizes of words arrays have to have the same length.'));
        }

        $this->words = $words;
        $this->wordsSizes = $sizes;
    }
    
    public function getWords(): array
    {
        return $this->words;
    }
    
    public function getWordsSizes(): array
    {
        return $this->wordsSizes;
    }
    
    public function getPointsOfWordsLines(): array
    {
        return $this->pointsOfWordsLines;
    }
    
    public function translate($x, $y): void
    {
        parent::translate($x, $y);
        
        foreach($this->pointsOfWordsLines as $i => $point)
        {
            $this->pointsOfWordsLines[$i] = $point->translate($x, $y);
        }
    }
    
    public function addLinePart(LinePart $linePart): void
    {
        $this->lineParts[] = $linePart;
    }
    
    public function getLineParts()
    {
        return $this->lineParts;
    }
    
    public function removeLinePart(LinePart $linePart): void
    {
        $key = array_search($linePart, $this->lineParts, true);
        
        if($key !== false)
        {
            unset($this->lineParts[$key]);
        }
    }
    
    protected function setDataFromUnserialize(array $data)
    {
        parent::setDataFromUnserialize($data);
        
        $this->text = $data['text'];
    }
    
    protected function getDataForSerialize()
    {
        $data = parent::getDataForSerialize();
        
        $data['text'] = $this->text;
        
        return $data;
    }
    
    public function copy()
    {
        $copy = parent::copy();

        $copy->lineParts = array();

        return $copy;
    }
    
    public function isLeaf(): bool
    {
        return true;
    }
    
    public function isInline(): bool
    {
        return true;
    }

    protected function isAbleToExistsAboveCoord($yCoord): bool
    {
        $yCoord += $this->getAncestorWithFontSize()->getAttribute('line-height');
        return $this->getFirstPoint()->getY() > $yCoord;
    }
    
    public function flush(): void
    {
        $this->lineParts = array();

        parent::flush();
    }
}