<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Exception;

/**
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class InvalidResourceException extends InvalidArgumentException
{
    public static function invalidColorException($color, \Exception $previous = null): self
    {
        $message = 'Color "%s" is invalid.'.($previous ? ' '.$previous->getMessage() : '');

        return new self(sprintf($message, $color), 0, $previous);
    }

    public static function invalidImageException($imagePath, \Exception $previous = null): self
    {
        $message = 'Image "%s" can\'t be initialized.'.($previous ? ' '.$previous->getMessage() : '');

        return new self(sprintf($message, $imagePath), 0, $previous);
    }

    public static function unsupportetImageTypeException($imagePath): self
    {
        return new self(sprintf('Image type of "%s" is not supported. Supported types: jpeg, png and tiff.', $imagePath));
    }

    public static function invalidFontException($fontData, \Exception $previous = null): self
    {
        $message = 'Font "%s" is invalid.'.($previous ? ' '.$previous->getMessage() : '');

        return new self(sprintf($message, $fontData), 0, $previous);
    }

    public static function invalidPdfFileException($file, \Exception $previous = null): self
    {
        $message = 'PDF file "%s" is invalid.'.($previous ? ' '.$previous->getMessage() : '');

        return new self(sprintf($message, $file), 0, $previous);
    }

    public static function fileDosntExistException($file): self
    {
        return new self(sprintf('File "%s" dosn\'t exist.', $file));
    }
}