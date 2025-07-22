<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\InputStream;

/**
 * Input stream that is able to read data form string
 * 
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class StringInputStream implements InputStream
{
    private $currentIndex = 0;
    private $content;
    private ?int $contentLength = null;
    
    public function __construct($content)
    {
        $this->content = $content;
        $this->contentLength = strlen($this->content);
    }

    public function seek($index, $mode = self::SEEK_CUR): int
    {
        $newIndex = 0;
        switch($mode)
        {
            case self::SEEK_CUR:
                $newIndex = $this->currentIndex + $index;
                break;
            case self::SEEK_SET:
                $newIndex = $index;
                break;
            case self::SEEK_END:
                $newIndex = $this->contentLength + $index;
                break;
        }
        
        $this->currentIndex = $newIndex;
        
        return 0;
    }
    
    public function read($length): string
    {
        if($this->currentIndex >= $this->contentLength)
        {
            return '';
        }
        
        $last = $this->currentIndex + $length;
        
        if($last > $this->contentLength)
        {
            $last = $this->contentLength - $this->currentIndex;
        }
        
        $data = substr($this->content, $this->currentIndex, $length);
        $this->seek($length);

        return $data;
    }
    
    public function close(): void
    {
        $this->content = $this->contentLength = $this->currentIndex = null;
    }
    
    public function tell()
    {
        return $this->currentIndex;
    }
    
    public function size(): ?int
    {
        return $this->contentLength;
    }
}