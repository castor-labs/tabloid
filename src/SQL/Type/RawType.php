<?php

namespace Castor\Tabloid\SQL\Type;

use Castor\Tabloid\SQL\Type;

/**
 * RawType passes the value as it is to and from the database
 */
final class RawType implements Type
{
    private static ?RawType $instance = null;

    public static function instance(): RawType
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function toDatabaseValue(mixed $php): mixed
    {
        return $php;
    }

    public function toPhpValue(mixed $db): mixed
    {
        return $db;
    }
}