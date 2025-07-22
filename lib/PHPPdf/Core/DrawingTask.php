<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core;

use PHPPdf\Core\Document;

/**
 * Encapsulate drawing task (callback + arguments + priority + order)
 *
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class DrawingTask
{
    private $callback;
    private array $arguments;
    private $priority;
    private ?int $order = null;

    public function __construct($callback, array $arguments = array(), $priority = Document::DRAWING_PRIORITY_FOREGROUND2)
    {
        $this->callback = $callback;
        $this->arguments = $arguments;
        $this->priority = $priority;
    }
    
    /**
     * @throws PHPPdf\Core\Exception\DrawingException If error occurs while drawing
     */
    public function invoke(): mixed
    {
        return call_user_func_array($this->callback, $this->arguments);
    }

    public function getPriority()
    {
        return $this->priority;
    }

    public function getOrder(): ?int
    {
        return $this->order;
    }

    public function setOrder($order): void
    {
        $this->order = (int) $order;
    }
    
    public function compareTo(DrawingTask $task): int|float
    {
        $diff = ($this->priority - $task->priority);

        if($diff === 0)
        {
            return ($task->order - $this->order);
        }
        else
        {
            return $diff;
        }
    }
}