<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Core\Parser;

use PHPPdf\Cache\Cache;

/**
 * Styleshet constraint with cache feature
 *
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class CachingStylesheetConstraint extends StylesheetConstraint
{
    private $resultMap = array();
    private bool $resultMapModified = false;
    private string $cacheId = '';

    public function find(array $query)
    {
        $queryAsString = $this->transformQueryToString($query);

        if(isset($this->resultMap[$queryAsString]))
        {
            $bag = $this->resultMap[$queryAsString];
        }
        else
        {
            $bag = parent::find($query);
            $this->resultMap[$queryAsString] = $bag;
            $this->setResultMapModified(true);
        }

        return $bag;
    }

    private function transformQueryToString(array $query): string
    {
        $queryParts = array();
        foreach($query as $queryElement)
        {
            $tag = $queryElement['tag'];
            $classes = $queryElement['classes'];

            $queryParts[] = sprintf('%s.%s', $tag, implode('.', $classes));
        }

        return implode(' ', $queryParts);
    }

    private function setResultMapModified(bool $flag): void
    {
        $this->resultMapModified = (bool) $flag;
    }

    public function isResultMapModified(): bool
    {
        return $this->resultMapModified;
    }

    protected function getDataToSerialize(): array
    {
        $data = parent::getDataToSerialize();

        $data['resultMap'] = $this->resultMap;
        $data['cacheId'] = $this->cacheId;

        return $data;
    }

    protected function restoreDataAfterUnserialize(array $data)
    {
        parent::restoreDataAfterUnserialize($data);

        $this->resultMap = $data['resultMap'];
        $this->setCacheId($data['cacheId']);
    }

    public function setCacheId($id): void
    {
        $this->cacheId = (string) $id;
    }

    public function getCacheId(): string
    {
        return $this->cacheId;
    }
    
    public static function merge(array $containers): static
    {
        $resultContainer = parent::merge($containers);
        
        $resultMap = array();
        foreach($containers as $container)
        {
            foreach($container->resultMap as $tag => $bag)
            {
                if(!isset($resultMap[$tag]))
                {
                    $resultMap[$tag] = array();
                }
                
                $resultMap[$tag][] = $bag;
            }
        }
        
        foreach($resultMap as $tag => $bags)
        {
            $resultMap[$tag] = BagContainer::merge($bags);
        }
        
        $resultContainer->resultMap = $resultMap;

        return $resultContainer;
    }
}