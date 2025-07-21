<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Parser;

use PHPPdf\Core\Node\Node;
use PHPPdf\Core\AttributeBag;

/**
 * Class to encapsulate two bags: AttributeBag and ComplexAttributeBag
 *
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class BagContainer implements \Serializable
{
    protected \PHPPdf\Core\AttributeBag $attributeBag;
    protected float $weight;
    protected $order = 0;

    public function __construct(array $attributes = array(), $weight = 0)
    {
        $attributeBag = new AttributeBag();
        
        foreach($attributes as $name => $value)
        {
            $attributeBag->add($name, $value);
        }

        $this->attributeBag = $attributeBag;
        $this->weight = (double) $weight;
    }

    /**
     * @return AttributeBag
     */
    protected function getAttributeBag()
    {
        return $this->attributeBag;
    }
    
    public function getAll()
    {
        return $this->getAttributeBag()->getAll();
    }
    
    public function add($name, $value): void
    {
        $this->getAttributeBag()->add($name, $value);
    }

    public function setOrder($order): void
    {
        $this->order = (int) $order;
    }
    
    public function getOrder()
    {
        return $this->order;
    }

    public function getWeight()
    {
        return $this->weight;
    }

    public function addWeight($weight): void
    {
        $this->weight += $weight;
    }

    public function serialize()
    {
        return serialize($this->getDataToSerialize());
    }

    protected function getDataToSerialize(): array
    {
        return array(
            'attributes' => $this->getAttributeBag()->getAll(),
            'weight' => $this->weight,
        );
    }

    public function unserialize($serialized): void
    {
        $data = unserialize($serialized);

        $this->restoreDataAfterUnserialize($data);
    }

    protected function restoreDataAfterUnserialize(array $data)
    {
        $this->attributeBag = new AttributeBag($data['attributes']);
        $this->weight = (float) $data['weight'];
    }
    
    public function apply(Node $node): void
    {
        $attributes = $this->getAll();
        
        foreach($attributes as $name => $value)
        {
            if(is_array($value))
            {
                $node->mergeComplexAttributes($name, $value);
            }
            else
            {
                $node->setAttribute($name, $value);
            }
        }
    }

    /**
     * Marge couple of BagContainers into one object. 
     * 
     * Result of merging always is BagContainer.
     *
     * @param array $containers
     * @return BagContainer Result of merging
     */
    public static function merge(array $containers): static
    {
        $attributeBags = array();

        $weight = 0;
        foreach($containers as $container)
        {
            $weight = max($weight, $container->getWeight());
            $attributeBags[] = $container->getAttributeBag();
        }

        $container = new static();
        $container->attributeBag = AttributeBag::merge($attributeBags);
        $container->weight = $weight;
        
        return $container;
    }
}