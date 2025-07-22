<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Node\Behaviour;

use PHPPdf\Core\Point;
use PHPPdf\Core\Node\Node,
    PHPPdf\Core\Engine\GraphicsContext;

/**
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
abstract class Behaviour
{
    private bool $passive = false;
    
    public function attach(GraphicsContext $gc, Node $node): void
    {
        if(!$this->isPassive())
        {
            $this->doAttach($gc, $node);
        }
    }
    
    abstract protected function doAttach(GraphicsContext $gc, Node $node);
    
    protected static function getFirstPointOf(Node $node)
    {
        return self::getTranslatedPointOf($node, $node->getFirstPoint());
    }
    
    protected static function getDiagonalPointOf(Node $node)
    {
        return self::getTranslatedPointOf($node, $node->getDiagonalPoint());
    }
    
    private static function getTranslatedPointOf(Node $node, Point $point)
    {
        $translation = $node->getPositionTranslation();
        
        return $point->translate($translation->getX(), $translation->getY());
    }

    public function isPassive()
    {
        return $this->passive;
    }

    public function setPassive($flag): void
    {
        $this->passive = (boolean) $flag;
    }
    
    public function getUniqueId()
    {
        return spl_object_hash($this);
    }
}