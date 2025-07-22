<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Node;

/**
 * Wrapper/placeholder for node object
 * 
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
final class NodeWrapper implements NodeAware
{
    private ?\PHPPdf\Core\Node\Node $node;
    
    public function __construct(Node $node = null)
    {
        $this->node = $node;
    }
    
    public function getNode(): ?\PHPPdf\Core\Node\Node
    {
        return $this->node;
    }
    
    public function setNode(Node $node): void
    {
        $this->node = $node;
    }
}