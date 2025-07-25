<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Formatter;

use PHPPdf\Core\Formatter\BaseFormatter,
    PHPPdf\Core\Node\Node,
    PHPPdf\Core\Document;

/**
 * Calculates real dimension of node
 *
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class StandardDimensionFormatter extends BaseFormatter
{
    public function format(Node $node, Document $document): void
    {
        $parent = $node->getParent();
        $maxWidth = $node->getRecurseAttribute('max-width') ?: \PHP_INT_MAX;
        $maxHeight = $node->getRecurseAttribute('max-height') ?: \PHP_INT_MAX;

        if($node->getWidth() === null && !$node->isInline() && $node->getFloat() === Node::FLOAT_NONE)
        {
            $parentWidth = $parent->getWidthWithoutPaddings();

            $marginLeft = $node->getMarginLeft();
            $marginRight = $node->getMarginRight();

            $node->setWidth(min($parentWidth - ($marginLeft + $marginRight), $maxWidth));
            $node->setRelativeWidth('100%');
        }
        elseif($node->isInline())
        {
            $node->setWidth(0);
        }

        if($node->getHeight() === null)
        {
            $node->setHeight(0);
        }

        $paddingLeft = $node->getPaddingLeft();
        $paddingRight = $node->getPaddingRight();
        $paddingTop = $node->getPaddingTop();
        $paddingBottom = $node->getPaddingBottom();
        
        $prefferedWidth = $node->getRealWidth() + $paddingLeft + $paddingRight;
        
        $parent = $node->getParent();
        
        $parentWidth = $parent ? $parent->getWidthWithoutPaddings() : null;
        
        if($parent && $parentWidth < $prefferedWidth)
        {
            $prefferedWidth = $parentWidth;
        }

        $node->setWidth(min($prefferedWidth, $maxWidth));
        $node->setHeight(min($node->getRealHeight() + $paddingTop + $paddingBottom, $maxHeight));
    }
}