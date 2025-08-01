<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Node;

use PHPPdf\Exception\LogicException;
use PHPPdf\Core\DrawingTaskHeap;
use PHPPdf\Core\Document;
use PHPPdf\Core\Node\Container;
use PHPPdf\Core\Formatter\Formatter;

/**
 * Collection of the pages
 *
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class PageCollection extends Container
{
    public function getAttribute($name): null
    {
        return null;
    }

    public function setAttribute($name, $value): static
    {
        return $this;
    }

    public function breakAt($height): never
    {
        throw new LogicException('PageCollection can\'t be broken.');
    }
    
    public function getGraphicsContext(): null
    {
        return null;
    }
    
    public function getAllDrawingTasks(Document $document): \PHPPdf\Core\DrawingTaskHeap
    {
        $tasks = new DrawingTaskHeap();
        $this->collectOrderedDrawingTasks($document, $tasks);
        $this->collectUnorderedDrawingTasks($document, $tasks);
        $this->collectPostDrawingTasks($document, $tasks);
        
        return $tasks;
    }
    
    public function collectPostDrawingTasks(Document $document, DrawingTaskHeap $tasks): void
    {
        foreach($this->getChildren() as $child)
        {
            $child->collectPostDrawingTasks($document, $tasks);
        }
    }
}