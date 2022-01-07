<?php

namespace Castor\Tabloid\Metadata;

use Exception;
use Throwable;

class ValidationError extends Exception
{
    public static function general(string $class, string $message): ValidationError
    {
        return new self(sprintf(
            'Validation error on metadata for class %s: %s',
            $class,
            $message,
        ));
    }
}