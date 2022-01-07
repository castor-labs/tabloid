<?php

namespace Castor\Tabloid\Engine\MySQL;

use Castor\Tabloid\SQL\Type;

abstract class BaseType implements Type
{
    /**
     * @param string $type
     * @return bool
     */
    abstract public function isValidDatabaseType(string $type): bool;
}