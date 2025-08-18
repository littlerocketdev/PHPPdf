<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core;

/**
 * Heap of drawing tasks.
 *
 * Role of this heap is sort tasks by priority.
 *
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class DrawingTaskHeap extends \SplHeap
{
    private int $elements = 0;

    public function insert(mixed $value): bool
    {
        $value->setOrder($this->elements++);
        return parent::insert($value);
    }

    public function compare(mixed $value1, mixed $value2): int
    {
        return $value1->compareTo($value2);
    }
}
