<?php

function get_examples(): array
{
    $examples = array();
    
    $iter = new GlobIterator(__DIR__.'/*.xml');
    foreach($iter as $file)
    {
        $name = $file->getBasename('.xml');
        if(!str_contains($name, '-style'))
        {
            $examples[] = $name;
        }
    }
    
    return $examples;
}
