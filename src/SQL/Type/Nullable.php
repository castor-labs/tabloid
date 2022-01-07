<?php

namespace Castor\Tabloid\SQL\Type;

use Castor\Tabloid\SQL\Type;

/**
 * Nullable is a type that wraps any type that is marked as nullable in the
 * metadata.
 */
class Nullable implements Type
{
    private Type $type;

    public function __construct(Type $type)
    {
        $this->type = $type;
    }

    public function toDatabaseValue(mixed $php): mixed
    {
        if (null === $php) {
            return null;
        }

        return $this->type->toDatabaseValue($php);
    }

    public function toPhpValue(mixed $db): mixed
    {
        if (null === $db) {
            return null;
        }

        return $this->type->toPhpValue($db);
    }
}