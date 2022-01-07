<?php

namespace Castor\Tabloid\Engine\MySQL;

use Castor\Tabloid\SQL\Type;

class TBoolean extends BaseType
{
    public const TYPES = ['tinyint'];

    public function toDatabaseValue(mixed $php): int
    {
        if (!is_bool($php)) {
            throw new Type\ConversionError('Value sent to database should be a boolean');
        }

        return $php ? 1 : 0;
    }

    public function toPhpValue(mixed $db): bool
    {
        if ($db === '1') {
            $db = true;
        }
        if ($db === '0') {
            $db = false;
        }

        if (!is_bool($db)) {
            throw new Type\ConversionError('Value fetch from database should be a boolean');
        }

        return $db;
    }

    public function isValidDatabaseType(string $type): bool
    {
        return in_array($type, self::TYPES, true);
    }
}