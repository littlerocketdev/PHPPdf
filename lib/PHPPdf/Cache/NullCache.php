<?php

/*
 * Copyright 2011 Piotr Śliwa <peter.pl7@gmail.com>
 *
 * License information is in LICENSE file
 */

namespace PHPPdf\Cache;

/**
 * Dummy cache class. Used when cache is no used.
 *
 * @author Piotr Śliwa <peter.pl7@gmail.com>
 */
class NullCache implements Cache
{
    public static function getInstance(): self
    {
        return new self();
    }

    public function load($id): bool
    {
        return false;
    }

    public function test($id): bool
    {
        return false;
    }

    public function save($id, $value): bool
    {
        return true;
    }

    public function remove($id): bool
    {
        return true;
    }
}
