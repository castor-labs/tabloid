<?php

namespace Castor\Tabloid\SQL\Type;

use Castor\Tabloid\SQL\Type;

/**
 * A Registry is an interface that obtains types from the registry.
 */
interface Registry
{
    /**
     * @throws NotFound
     */
    public function get(string $type): Type;
}