<?php

namespace Castor\Tabloid;

use Countable;
use Traversable;

/**
 * @template T
 */
interface Collection extends Countable, Traversable
{
    /**
     * Slices a collection of objects
     *
     * @param int $offset
     * @param int|null $limit
     * @return Collection<T>
     */
    public function slice(int $offset = 0, int $limit = null): Collection;

    /**
     * @return int
     */
    public function count(): int;
}