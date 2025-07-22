<?php


namespace PHPPdf\Core\Parser;


class DocumentParsingContext
{
    private bool $inPlaceholder = false;
    private bool $inBehaviour = false;

    public function isInPlaceholder(): bool
    {
        return $this->inPlaceholder;
    }

    public function isInBehaviour(): bool
    {
        return $this->inBehaviour;
    }

    public function enterPlaceholder(): void
    {
        $this->inPlaceholder = true;
    }

    public function exitPlaceholder(): void
    {
        $this->inPlaceholder = false;
    }

    public function enterBehaviour(): void
    {
        $this->inBehaviour = true;
    }

    public function exitBehaviour(): void
    {
        $this->inBehaviour = false;
    }
} 